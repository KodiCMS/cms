<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Plugins
 * @author		ButscHSter
 */
class Plugin_Decorator extends Plugin {
	
	const TABLE_NAME = 'plugins';
	const CACHE_KEY = 'plugins';
	
	/**
	 *
	 * @var array
	 */
	protected $_settings = array();
	
	/**
	 *
	 * @var array
	 */
	protected $_info = array();

	public function __construct( $id, array $info )
	{
		$this->_info['id'] = strtolower($id);
		$this->_info['path'] = PLUGPATH . $this->id() . DIRECTORY_SEPARATOR;
		
		if( ! isset($info['title']) )
		{
			throw new Plugin_Exception('Plugin title for plugin :id not set', array(
				':id' => $this->id()
			));
		}
		
		foreach ($info as $key => $value)
		{
			$this->_info[$key] = $value;
		}
		
		$this->_info['icon'] = file_exists( $this->path() . 'icon.png') 
			? PLUGINS_URL . $this->id() . '/' . 'icon.png'
			: NULL;
		
		if( $this->is_installed() )
		{
			$this->_load_settings();
			$this->_load_installed();
		}
	}
	
	/**
	 * 
	 * @return string
	 */
	public function id()
	{
		return Arr::get($this->_info, 'id');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function title()
	{
		return Arr::get($this->_info, 'title');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function description()
	{
		return Arr::get($this->_info, 'description');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function version()
	{
		return Arr::get($this->_info, 'version');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function author()
	{
		return Arr::get($this->_info, 'author');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function path()
	{
		return Arr::get($this->_info, 'path');
	}

	/**
	 * 
	 * @return string
	 */
	public function icon()
	{
		return Arr::get($this->_info, 'icon');
	}

	public function __set( $key, $value )
	{
		return $this->set( $key, $value );
	}
	
	public function __get( $key )
	{
		return $this->get( $key );
	}

	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return \Plugin_Decorator
	 */
	public function set( $key, $value = NULL )
	{
		$this->_settings[$key] = $value;
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return \Plugin_Decorator
	 */
	public function set_settings( array $data )
	{
		foreach ($data as $k => $v)
		{
			$this->set($k, $v);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get( $key, $default = NULL )
	{
		return Arr::get($this->settings(), $key, $default);
	}

	/**
	 * 
	 * @return array
	 */
	public function default_settings()
	{
		return array();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function settings()
	{
		return Arr::merge($this->default_settings(), $this->_settings);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function is_installed()
	{
		return Plugins::is_installed( $this->id() );
	}

	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	public function install()
	{
		$data = array(
			'id' => $this->id(),
			'title' => $this->title(),
			'settings' => serialize( $this->settings() )
		);

		$result = DB::insert( self::TABLE_NAME )
			->columns(array_keys($data))
			->values($data)
			->execute();

		Kohana::modules( Kohana::modules() + array('plugin_' . $this->id() => PLUGPATH . $this->id()) );

		$this->_clear_cache();
		
		$schema_file = $this->path() . 'install' . DIRECTORY_SEPARATOR . 'schema.sql';
		if( file_exists( $schema_file ))
		{
			Database_Helper::insert_sql(file_get_contents($schema_file));
		}
		
		$install_file = $this->path() . 'install' . EXT;

		if( file_exists( $install_file ))
		{
			Kohana::load($install_file);
		}
		
		Observer::notify('plugin_install', $this->id());
		
		Plugins::install( $this );
	}
	
	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	public function uninstall( $run_script = FALSE )
	{
		$this->_status = (bool) DB::delete( self::TABLE_NAME )
			->where('id', '=', $this->id())
			->execute();
		
		Plugins::uninstall( $this );
		
		$drop_file = $this->path() . 'install' . DIRECTORY_SEPARATOR . 'drop.sql';
		if( file_exists( $drop_file ))
		{
			Database_Helper::insert_sql(file_get_contents($drop_file));
		}

		$uninstall_file = $this->path() . 'uninstall' . EXT;
		if($run_script === TRUE AND file_exists( $uninstall_file ))
		{
			Kohana::load($uninstall_file);
		}

		Observer::notify('plugin_uninstall', $this->id());

		return $this->_clear_cache();
	}
	
	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	public function register()
	{
		Plugins::register( $this );
		return $this;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function has_settings_page()
	{
		return file_exists($this->path() . 'views' . DIRECTORY_SEPARATOR . $this->id() . DIRECTORY_SEPARATOR . 'settings.php');
	}

	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	protected function _load_settings()
	{
		$settings = DB::select('settings')
			->from( self::TABLE_NAME )
			->where('id', '=', $this->id())
			->cache_key(Plugin_Decorator::CACHE_KEY . '::plugin::' . $this->id())
			->cached(Date::DAY)
			->limit(1)
			->execute()
			->get('settings');
		
		$this->_settings = !empty($settings) 
			? unserialize($settings) 
			: array();
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	protected function _load_installed()
	{
		extract( array('plugin' => $this), EXTR_SKIP );

		if(  file_exists( $this->path() . 'frontend' . EXT ) AND ! IS_BACKEND )
		{
			if(Kohana::$profiling === TRUE)
			{
				$benchmark = Profiler::start('Frontend plugins', $this->title());
			}
		
			include $this->path() . 'frontend' . EXT;
			
			if(isset($benchmark))
			{
				Profiler::stop($benchmark);
			}
		}
		
		if(  file_exists( $this->path() . 'backend' . EXT ) AND IS_BACKEND )
		{
			if(Kohana::$profiling === TRUE)
			{
				$benchmark = Profiler::start('Backend plugins', $this->title());
			}
			
			include $this->path() . 'backend' . EXT;
			
			if(isset($benchmark))
			{
				Profiler::stop($benchmark);
			}
		}
		
		return $this;
	}
	
	
	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	public function save_settings()
	{
		$status = (bool) DB::update( self::TABLE_NAME )
			->set(array(
				'settings' => serialize($this->settings())
			))
			->where('id', '=', $this->id())
			->execute();

		return $this->_clear_cache();
	}

	/**
	 * 
	 * @return \Plugin_Decorator
	 */
	protected function _clear_cache()
	{
		if(Kohana::$caching === TRUE)
		{
			$cache = Cache::instance();

			$cache->delete('Database::cache('.self::CACHE_KEY . '::list)');
			$cache->delete('Database::cache('.self::CACHE_KEY . '::plugin::' . $this->id() . ')');

			Kohana::cache('Route::cache()', NULL, -1);
			Kohana::cache('Kohana::find_file()', NULL, -1);
		}

		return $this;
	}
}