<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Installer
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Install extends Controller_System_Frontend 
{
	public $template = 'system/frontend';
	
	/**
	 *
	 * @var Validation 
	 */
	protected $_validation = NULL;
	
	/**
	 *
	 * @var Database 
	 */
	protected $_db_instance = NULL;

	public function action_error()
	{
		$this->template->title = __( ':cms_name &rsaquo; error', array(':cms_name' => CMS_NAME ));
		$this->template->content = View::factory('install/error', array(
			'title' => $this->template->title
		));
	}
	
	public function action_index()
	{
		Assets::js('steps', ADMIN_RESOURCES . 'libs/steps/jquery.steps.min.js', 'jquery');
		Assets::css('select2', ADMIN_RESOURCES . 'libs/select2/select2.css', 'jquery');
		Assets::js('select2', ADMIN_RESOURCES . 'libs/select2/select2.min.js', 'jquery');
		
		Assets::js('install', ADMIN_RESOURCES . 'js/install.js', 'global');
		Assets::css('install', ADMIN_RESOURCES . 'css/install.css', 'global');

		$this->template->title = __( ':cms_name &rsaquo; installation', array(':cms_name' => CMS_NAME ) );

		$data = array(
			'db_driver' => 'mysql',
			'db_server' => '127.0.0.1',
			'db_port' => 3306,
			'db_user' => 'root',
			'db_name' => 'kodicms',
			'site_name' => CMS_NAME,
			'username' => 'admin',
			'email' => 'admin@yoursite.com',
			'admin_dir_name' => 'backend',
			'url_suffix' => '.html',
			'password_generate' => FALSE,
			'timezone' => date_default_timezone_get(),
			'cache_type' => 'sqlite',
			'locale' => I18n::lang(),
			'insert_test_data' => FALSE
		);

		$this->template->content = View::factory('install/index', array(
			'data' => Session::instance()->get_once( 'install_data', $data ),
			'env_test' => View::factory('install/env_test'),
			'cache_types' => Kohana::$config->load('installer')->get( 'cache_types', array() ),
			'title' => $this->template->title
		));
	}

	/**
	 * 
	 * @throws Installer_Exception
	 */
	public function action_go()
	{
		$this->auto_render = FALSE;
		
		$post = $this->request->post('install');

		if ( empty($post) )
		{
			throw new Installer_Exception( 'No install data!' );
		}
		
		if(isset($post['password_generate']))
		{
			$post['password_field'] = Text::random();
		}
		
		date_default_timezone_set( $post['timezone'] );

		Session::instance()
			->set( 'install_data', $post );

		try
		{
			$this->_validation = $this->_valid($post);
			$this->_db_instance = $this->_connect_to_db($post);
			
			Database::$default = 'install';
		}
		catch (Validation_Exception $e)
		{
			$this->_show_error($e);
		}
		
		try 
		{
			if(isset($post['empty_database']))
			{
				$this->_reset();
			}
			
			$this->_import_shema($post);
			$this->_import_dump($post);
			$this->_install_plugins($post);
			$this->_install_modules($post);
			$this->_create_site_config($post);
			$this->_create_config_file($post);
		}
		catch (Exception $e)
		{
			$this->_show_error($e);
		}
		
		$this->_complete($post);
	}
	
	/**
	 * 
	 * @param array $post
	 */
	protected function _complete($post)
	{
		if(PHP_SAPI == 'cli')
		{
			Minion_CLI::write('==============================================');
			Minion_CLI::write(__('KodiCMS installed succefully'));
			Minion_CLI::write('==============================================');

			$install_data = Session::instance()->get_once('install_data');
			Minion_CLI::write(__('Login: :login', array(':login' => Arr::get($install_data, 'username'))));
			Minion_CLI::write(__('Password: :password', array(':password' => Arr::get($install_data, 'password_field'))));
			exit();
		}
		
		$this->go($post['admin_dir_name'] . '/login');
	}

	/**
	 * Вывод ошибок
	 * @param Exception $e
	 */
	protected function _show_error(Exception $e)
	{
		if(PHP_SAPI == 'cli')
		{
			Minion_CLI::write(__(':text | :file [:line]', array(
				':text' => $e->getMessage(),
				':file' => $e->getFile(),
				':line' => $e->getLine()
			)));
			exit();
		}
		
		Messages::errors($e->getMessage());
		$this->go_back();
	}

	/**
	 * Создание коннекта к БД
	 * 
	 * @param array $post
	 * @return Database
	 * @throws Validation_Exception
	 */
	protected function _connect_to_db(array $post)
	{
		$config = Kohana::$config->load('database');
		
		
		switch ($post['db_driver'])
		{
			case 'mysql':
				$connection = array(
					'hostname' => $post['db_server'] . ':' . $post['db_port'],
					'database' => $post['db_name'],
					'username' => $post['db_user'],
					'password' => $post['db_password'],
					'persistent' => FALSE,
				);
				break;
			case 'pdo':
				$connection = array(
					'dsn'        => 'mysql:host='.$post['db_server'].';port='.$post['db_port'].';dbname='.$post['db_name'],
					'username'   => $post['db_user'],
					'password'   => $post['db_password'],
					'persistent' => FALSE,
				);
				break;
		}
				
		$config->set('install', array(
			'type' => $post['db_driver'],
			'connection' => $connection,
			'table_prefix' => $post['db_table_prefix'],
			'charset' => 'utf8',
			'caching' => FALSE,
			'profiling' => TRUE
		));

		$db = Database::instance( 'install');

		try
		{
			$db->connect();
			
			return $db;
		} 
		catch (Database_Exception $exc)
		{
			switch ($exc->getCode())
			{
				case 1049:
					$this->_validation->error( 'db_name' , 'incorrect' );
					break;
				case 2:
					$this->_validation
						->error( 'db_server' , 'incorrect' )
						->error( 'db_user' , 'incorrect' )
						->error( 'db_password' , 'incorrect' );
					break;
			}

			throw new Validation_Exception($this->_validation, $exc->getMessage(), NULL, $exc->getCode());
		}
	}

	/**
	 * Проверка данных формы
	 * 
	 * @param array $data
	 * @return Validation
	 * @throws Validation_Exception
	 */
	protected function _valid(array $data)
	{
		$cache_types = Kohana::$config->load('installer')->get( 'cache_types', array() );

		$validation = Validation::factory( $data )
			->rule( 'db_server', 'not_empty' )
			->rule( 'db_user', 'not_empty' )
			->rule( 'db_name', 'not_empty' )
			->rule( 'admin_dir_name', 'not_empty' )
			->rule( 'username', 'not_empty' )
			->rule( 'email', 'not_empty' )
			->rule( 'email', 'email' )
			->rule( 'cache_type', 'not_empty')
			->rule( 'cache_type', 'in_array', array(':value', array_keys( $cache_types )))
			->label('db_server', __('Database server'))
			->label('db_user', __( 'Database user' ))
			->label('db_password', __( 'Database password' ))
			->label('db_name', __( 'Database name' ))
			->label('admin_dir_name', __( 'Admin dir name' ))
			->label('username', __( 'Administrator username' ))
			->label('email', __( 'Administrator email' ))
			->label('password_field', __( 'Password' ))
			->label('cache_type', __( 'Cache type' ));
		
		if(!isset($data['password_generate']))
		{
			$validation
				->rule('password_field', 'min_length', array(':value', Kohana::$config->load('auth')->get( 'password_length' )))
				->rule('password_field', 'not_empty')
				->rule('password_confirm', 'matches', array(':validation', ':field', 'password_field'))
				->label('password_confirm', __( 'Confirm Password' ));
		}

		if ( !$validation->check() )
		{
			throw new Validation_Exception($validation);
		}
		
		return $validation;
	}

	/**
	 * Импорт схемы БД из файла `schema.sql`
	 * @param array $post
	 * @throws Installer_Exception
	 */
	protected function _import_shema($post)
	{		
		// Merge modules schema.sql
		$schema_content = $this->_merge_module_files('schema.sql');
		
		$schema_content = str_replace( '__TABLE_PREFIX__', $post['db_table_prefix'], $schema_content );

		if ( !empty( $schema_content ) )
		{
			$this->_insert_data($schema_content);
		}
	}
	
	/**
	 * Импорт данных из файла `dump.sql`
	 * 
	 * @param array $post
	 * @throws Installer_Exception
	 */
	protected function _import_dump($post)
	{
		// Merge modules dump.sql
		$dump_content = $this->_merge_module_files('dump.sql');
		
		$replace = array(
			'__EMAIL__'				=> Arr::get($post, 'email'),
			'__USERNAME__'			=> Arr::get($post, 'username'),
			'__TABLE_PREFIX__'		=> $post['db_table_prefix'],
			'__ADMIN_PASSWORD__'	=> Auth::instance()->hash($post['password_field']),
			'__DATE__'				=> date('Y-m-d H:i:s'),
			'__LANG__'				=> Arr::get($post, 'locale')
		);
		
		$dump_content = str_replace(
			array_keys( $replace ), array_values( $replace ), $dump_content
		);

		if ( ! empty( $dump_content ) )
		{
			$this->_insert_data($dump_content);
		}
	}
	
	/**
	 * Запись в БД данных в таблицу config
	 * Значения по умолчанию устанавливаются в конфиг файле `installer`
	 * 
	 * @param array $post
	 */
	protected function _create_site_config($post)
	{
		$config_values = Kohana::$config->load('installer')->get('default_config', array());
		
		$config_values['site']['title'] = Arr::get($post, 'site_name');
		$config_values['site']['default_locale'] = Arr::get($post, 'locale');
		$config_values['email']['default'] = Arr::get($post, 'email');

		$insert = DB::insert('config', array('group_name', 'config_key', 'config_value'));
		foreach ($config_values as $group => $data)
		{
			foreach ($data as $key => $config)
			{
				$insert->values(array($group, $key, serialize($config)));
					
			}
		}
		
		$insert->execute($this->_db_instance);
	}
	
	/**
	 * Установка пллагинов по умолчанию
	 * 
	 * Список плагинов по умолчанию указывается в конфиг файле `installer`
	 */
	protected function _install_plugins($post)
	{
		if( ! is_dir(MODPATH . 'plugins') ) return;

		Kohana::modules(Kohana::modules() + array('plugins'	=> MODPATH . 'plugins'));

		$default_plugins = Kohana::$config->load('installer')->get('default_plugins', array());
		
		Plugins::find_all();
		
		if(!empty($post['insert_test_data']))
		{
			$default_plugins[] = 'test';
		}

		foreach ($default_plugins as $name)
		{
			$plugin = Plugins::get_registered( $name );
			
			if($plugin instanceof Plugin_Decorator)
			{
				$plugin->install();
			}
		}
	}
	
	/**
	 * Используется для установки данных из модулей
	 * 
	 * Метод проходится по модулям, ищет в них файл install.php, если существует
	 * запускает его и передает массив $post
	 *
	 * @param type $post
	 * @return type
	 */
	protected function _install_modules($post)
	{
		if ( ! is_dir(MODPATH) ) return;
		
		// Create a new directory iterator
		$path = new DirectoryIterator(MODPATH);
		
		foreach ($path as $dir)
		{
			if($dir->isDot()) continue;
			$file_name = MODPATH . $dir->getBasename() . DIRECTORY_SEPARATOR . 'install' . EXT;
			if(file_exists($file_name))
			{
				include $file_name;
			}
		}
	}

	/**
	 * Создание конфиг файла
	 * 
	 * @param array $post
	 * @throws Installer_Exception
	 */
	protected function _create_config_file($post)
	{
		$tpl_file = INSTALL_DATA . 'config.tpl';
		
		if ( ! file_exists( $tpl_file ) )
		{
			throw new Installer_Exception( 'Config template file :file not found!', array(
				':file' => $tpl_file
			) );
		}

		// Insert settings to config.php		
		$tpl_content = file_get_contents( $tpl_file );

		$repl = array(
			'__DB_TYPE__'			=> $post['db_driver'],
			'__DB_SERVER__'			=> $post['db_server'],
			'__DB_NAME__'			=> $post['db_name'],
			'__DB_USER__'			=> $post['db_user'],
			'__DB_PASS__'			=> $post['db_password'],
			'__TABLE_PREFIX__'		=> $post['db_table_prefix'],
			'__URL_SUFFIX__'		=> $post['url_suffix'],
			'__ADMIN_DIR_NAME__'	=> $post['admin_dir_name'],
			'__TIMEZONE__'			=> $post['timezone'],
			'__COOKIE_SALT__'		=> Text::random('alnum', 16),
			'__CACHE_TYPE__'		=> $post['cache_type']
		);

		$tpl_content = str_replace(
			array_keys( $repl ), array_values( $repl ), $tpl_content
		);

		if ( ! file_put_contents( CFGFATH, $tpl_content ) !== FALSE )
		{
			throw new Installer_Exception( 'Can\'t write config.php file!' );
		}
	}

	/**
	 * Вставка SQL строк в БД
	 * 
	 * @param array $data
	 * @throws Validation_Exception
	 */
	protected function _insert_data($data)
	{
		$data = preg_split( '/;(\s*)$/m', $data );

		try
		{
			DB::query(NULL, 'SET FOREIGN_KEY_CHECKS = 0')
				->execute($this->_db_instance);
			
			foreach($data as $sql)
			{
				if(empty($sql))
				{
					continue;
				}

				DB::query(Database::INSERT, $sql)
					->execute($this->_db_instance);
			}
			
			DB::query(NULL, 'SET FOREIGN_KEY_CHECKS = 1')
				->execute($this->_db_instance);
		} 
		catch (Database_Exception $exc)
		{
			switch ($exc->getCode())
			{
				case 1005:
					$this->_validation->error( 'db_name' , 'database_not_empty' );
					break;
			}

			throw new Validation_Exception($this->_validation, $exc->getMessage(), NULL, $exc->getCode());
		}
	}
	
	/**
	 * Очистка указанной БД от записей
	 */
	protected function _reset()
	{
		DB::query(NULL, 'SET FOREIGN_KEY_CHECKS = 0')
			->execute($this->_db_instance);

		$tables = DB::query(Database::SELECT, 'SHOW TABLES')
			->execute($this->_db_instance);
		
		foreach ($tables as $table) 
		{
			$table = array_values($table);
			$table_name = $table[0];

			DB::query(NULL, 'DROP TABLE `:table_name`')
				->param( ':table_name', DB::expr($table_name) )
				->execute($this->_db_instance);
		}
		
		if(  file_exists( CFGFATH ) !== FALSE )
		{
			unlink(CFGFATH);
		}
		
		DB::query(NULL, 'SET FOREIGN_KEY_CHECKS = 1')
			->execute($this->_db_instance);
	}
	
	/**
	 * Сбор текста из файла модулей в переменную
	 * 
	 * @param string $filename
	 * @param string $content
	 * @return string
	 */
	protected function _merge_module_files($filename, $content = '')
	{
		// Create a new directory iterator
		$path = new DirectoryIterator(MODPATH);

		foreach ($path as $dir)
		{
			if($dir->isDot()) continue;
			$file_name = MODPATH . $dir->getBasename() . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . $filename;
			if(file_exists($file_name))
			{
				$content .= "\n";
				$content .= file_get_contents( $file_name );
			}
		}
		
		return $content;
	}
}