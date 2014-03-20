<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

abstract class KodiCMS_Search {
	
	/**
	 * @var   Search instances
	 */
	public static $instances = array();
	
	/**
	 * @var   string     default driver to use
	 */
	public static $default = 'native';
	
	/**
	 * 
	 * @param string $group
	 * @return type
	 * @throws Kohana_Exception
	 */
	public static function instance($group = NULL)
	{
		// If there is no group supplied
		if ($group === NULL)
		{
			// Use the default setting
			$group = Search::$default;
		}

		if (isset(Search::$instances[$group]))
		{
			// Return the current group if initiated already
			return Search::$instances[$group];
		}

		$config = Kohana::$config->load('search');

		if ( ! $config->offsetExists($group))
		{
			throw new Kohana_Exception(
				'Failed to load Kohana Search group: :group',
				array(':group' => $group)
			);
		}

		$config = $config->get($group);

		// Create a new search type instance
		$search_class = 'Search_'.ucfirst($config['driver']);
		Search::$instances[$group] = new $search_class($config);

		// Return the instance
		return Search::$instances[$group];
	}
	
	/**
	 * @var  Config
	 */
	protected $_config = array();

	/**
	 * Ensures singleton pattern is observed, loads the default expiry
	 *
	 * @param  array  $config  configuration
	 */
	protected function __construct(array $config)
	{
		$this->config($config);
	}

	/**
	 * Getter and setter for the configuration. If no argument provided, the
	 * current configuration is returned. Otherwise the configuration is set
	 * to this class.
	 *
	 * @param   mixed    key to set to array, either array or config path
	 * @param   mixed    value to associate with key
	 * @return  mixed
	 */
	public function config($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_config;

		if (is_array($key))
		{
			$this->_config = $key;
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_config, $key);

			$this->_config[$key] = $value;
		}

		return $this;
	}

	/**
	 * Overload the __clone() method to prevent cloning
	 *
	 * @return  void
	 * @throws  Cache_Exception
	 */
	final public function __clone()
	{
		throw new Cache_Exception('Cloning of Search objects is forbidden');
	}
	
	/**
	 * 
	 * @param string $module
	 * @param integer $id
	 * @param string $title
	 * @param string $content
	 * @param array $params
	 * @return bool
	 */
	abstract public function add_to_index( $module, $id, $title, $content = '', $annotation, $params = array() );
	
	/**
	 * 
	 * @param string $module
	 * @param integer $id
	 * @return bool
	 */
	abstract public function remove_from_index( $module, $id = NULL );
}