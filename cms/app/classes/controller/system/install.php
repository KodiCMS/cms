<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_System_Install extends Controller_System_Template {

	public $template = 'layouts/frontend';

	public function action_index()
	{
		$this->template->content = View::factory('system/install', array(
			'data' => Session::instance()->get( 'install_data', array( ) )
		));
	}

	public function action_go()
	{
		$this->auto_render = FALSE;

		if ( !isset( $_POST['install'] ) )
		{
			throw new  Kohana_Exception( 'Install data not found!' );
		}

		$post = $_POST['install'];

		$post['db_driver'] = DB_TYPE;
		$post['password'] = Text::random();
		//$post['admin_dir_name'] = 'admin';
		
		date_default_timezone_set( $post['timezone'] );

		Session::instance()
			->set( 'install_data', $post );

		$validation = Validation::factory( $post )
			->rule( 'db_server', 'not_empty' )
			->rule( 'db_user', 'not_empty' )
			->rule( 'db_name', 'not_empty' )
			->rule( 'admin_dir_name', 'not_empty' )
			->rule( 'username', 'not_empty' )
			->rule( 'email', 'not_empty' )
			->rule( 'email', 'email' );

		if ( !$validation->check() )
		{
			Messages::errors($validation->errors('validation'));
			$this->go_back();
		}

		$db = Database::instance( 'install', array(
			'type' => $post['db_driver'],
			'connection' => array(
				'hostname' => $post['db_server'],
				'database' => $post['db_name'],
				'username' => $post['db_user'],
				'password' => $post['db_password'],
				'persistent' => FALSE,
			),
			'table_prefix' => $post['table_prefix'],
			'charset' => 'utf8',
			'caching' => FALSE,
			'profiling' => TRUE
		) );

		try
		{
			$db->connect();
		} 
		catch (Database_Exception $exc)
		{
			Messages::errors($exc->getMessage());
			$this->go_back();
		}
		
		$this->_import_shema($post, $db);
		$this->_import_dump($post, $db);
		$this->_create_config($post);
		
		$this->go($post['admin_dir_name'] . '/login');
	}

	protected function _import_shema($post, $db)
	{
		$schema_file = CMSPATH . 'install' . DIRECTORY_SEPARATOR . 'schema.sql';
		
		if ( !file_exists( $schema_file ) )
		{
			throw new  Kohana_Exception( 'Database schema file not found!' );
		}

		// Create tables
		$schema_content = file_get_contents( $schema_file );
		$schema_content = str_replace( 'TABLE_PREFIX_', $post['table_prefix'], $schema_content );

		if ( !empty( $schema_content ) )
		{
			$this->_insert_data($schema_content, $db);
		}
	}
	
	protected function _import_dump($post, $db)
	{
		$dump_file = CMSPATH . 'install' . DIRECTORY_SEPARATOR . 'dump.sql';

		if ( !file_exists( $dump_file ) )
		{
			throw new  Kohana_Exception( 'Database dump file not found!' );
		}

		// Insert SQL dump
		$dump_content = file_get_contents( $dump_file );
		
		$replace = array(
			'__SITE_NAME__' => Arr::get($post, 'site_name', 'KoDi CMS'),
			'__EMAIL__' => Arr::get($post, 'email', 'admin@yoursite.com'),
			'__USERNAME__' => Arr::get($post, 'username', 'admin'),
			'TABLE_PREFIX_' => $post['table_prefix'],
			'__ADMIN_PASSWORD__' => Auth::instance()->hash($post['password']),
			'__DATE__' => date( 'Y-m-d H:i:s'),
			'__LANG__' => I18n::lang()
		);
		
		$dump_content = str_replace(
			array_keys( $replace ), array_values( $replace ), $dump_content
		);

		if ( !empty( $dump_content ) )
		{
			$this->_insert_data($dump_content, $db);
		}
	}
	
	protected function _create_config($post)
	{
		$tpl_file = CMSPATH . 'install' . DIRECTORY_SEPARATOR . 'config.tpl';

		// Insert settings to config.php		
		$tpl_content = file_get_contents( $tpl_file );

		$repl = array(
			'__DB_TYPE__' => $post['db_driver'],
			'__DB_SERVER__' => $post['db_server'],
			'__DB_NAME__' => $post['db_name'],
			'__DB_USER__' => $post['db_user'],
			'__DB_PASS__' => $post['db_password'],
			'__TABLE_PREFIX__' => $post['table_prefix'] . '_',
			'__URL_SUFFIX__' => $post['url_suffix'],
			'__ADMIN_DIR_NAME__' => $post['admin_dir_name'],
			'__LANG__' => I18n::lang(),
			'__TIMEZONE__' => $post['timezone'],
			'__COOKIE_SALT__' => Text::random('alnum', 16)
		);

		$tpl_content = str_replace(
			array_keys( $repl ), array_values( $repl ), $tpl_content
		);

		if ( !file_put_contents( DOCROOT . 'config' . EXT, $tpl_content ) !== FALSE )
		{
			throw new  Kohana_Exception( 'Can\'t write config.php file!' );
		}
	}
	
	protected function _insert_data($data, $db)
	{
		$data = preg_split( '/;(\s*)$/m', $data );

		foreach($data as $sql)
		{
			if(empty($sql))
			{
				continue;
			}

			try 
			{
				DB::query(Database::INSERT, $sql)
					->execute($db);
			}
			catch (Exception $e)
			{
				echo($e->getMessage());
				continue;
			}
		}
	}
}