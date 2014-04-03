<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @author		ButscHSter
 */
abstract class Kohana_SSO {
	
	public static function connected_accounts()
	{
		$accounts = Config::get('oauth.accounts');
		$providers = array();
		
		foreach ($accounts as $provider => $data)
		{
			if(
					(isset($data['id']) AND empty($data['id']))
				OR
					(isset($data['key']) AND empty($data['key']))		
				OR 
					empty($data['secret'])
			)
				continue;

			 $providers[$provider] = $data;
		}
		
		return $providers;
	}

	/**
	 * @var  SSO
	 */
	protected static $_instance;

	/**
	 * @static
	 * @return  SSO
	 */
	public static function instance()
	{
		if ( empty(SSO::$_instance))
		{
			$config = Kohana::$config->load('sso');
			SSO::$_instance = new SSO($config);
		}

		return SSO::$_instance;
	}

	/**
	 * @var  Config
	 */
	protected $_config;
	/**
	 * @var  Session
	 */
	protected $_session;

	protected $_driver_key    = 'sso_driver';

	/**
	 * @var  SSO_Driver[]  SSO driver collection
	 */
	protected $_drivers = array();
	/**
	 * @var SSO_ORM
	 */
	protected $_orm;

	protected function __construct($config = NULL)
	{
		$this->_config = $config;
		$session = Arr::get($config, 'session');
		$this->_session = Session::instance($session);
	}

	/**
	 * @param bool $refresh reload user data from DB
	 * @return  FALSE|Model_Auth_Data
	 */
	public function get_user($refresh = FALSE)
	{
		$driver = $this->_session->get($this->_driver_key);
		
		if ( ! $driver  )
		{
			return FALSE;
		}

		return $this->driver($driver)->get_user();
	}

	/**
	 * This method can use different param types and count depends on driver.
	 *
	 *      // try to log in via OAuth v2 as Github user (access token required)
	 *      SSO::instance()->login('oauth2.github', $token);
	 *
	 *
	 * @throws SSO_Exception
	 * @return  boolean
	 */
	public function login()
	{
		if (func_num_args() < 2)
		{
			throw new SSO_Exception('Minimum two params required to log in');
		}

		// automatically logout
		$this->logout();

		$params = func_get_args();
		$driver_name = array_shift($params);
		$driver = $this->driver($driver_name);
		if ($user = call_user_func_array(array($driver, 'login'), $params))
		{
			$this->_complete_login($driver_name);
			return TRUE;
		}

		return FALSE;
	}

	protected function _complete_login($driver = NULL)
	{
		$this->_session->set($this->_driver_key, $driver);
	}

	public function logout()
	{
		if ( ! $driver = $this->_session->get($this->_driver_key))
		{
			return TRUE;
		}

		$this->driver($driver)->logout();

		$this->_session
			->delete($this->_driver_key);
	}

	/**
	 * @param  string  $name  Driver type
	 *
	 * @throws SSO_Exception
	 * @return SSO_Driver
	 */
	public function driver($name = NULL)
	{
		if ($name === NULL AND ! $name = $this->_session->get($this->_driver_key))
		{
			throw new SSO_Exception('SSO driver name required');
		}

		// OAuth.Google will be a OAuth_Google driver
		$name = str_replace('.', '_', $name);
		if ( ! isset($this->_drivers[$name]))
		{
			$class = 'SSO_Driver_'.$name;
			$driver = new $class($this);
			$driver->init();
			$this->_drivers[$name] = $driver;
		}

		return $this->_drivers[$name];
	}

	/**
	 * @return SSO_ORM
	 */
	public function orm()
	{
		if ( ! $this->_orm)
		{
			$this->_orm = new SSO_ORM;
		}

		return $this->_orm;
	}
}