<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Behavior
 * @author		ButscHSter
 */
abstract class Behavior_Abstract {

	/**
	 *
	 * @var Model_Page_Front 
	 */
	protected $_page = NULL;
	
	/**
	 *
	 * @var Behavior_Route
	 */
	protected $_router = NULL;

	/**
	 *
	 * @var array 
	 */
	protected $_settings = NULL;

	public function __construct() 
	{
		$this->_router = new Behavior_Route($this->routes());
	}

	/**
	 * 
	 * @return array
	 */
	public function routes()
	{
		return array();
	}
	
	/**
	 * 
	 * @return Behavior_Route
	 */
	public function router()
	{
		return $this->_router;
	}

	/**
	 * 
	 * @param string $uri
	 */
	public function execute_uri($uri) 
	{
		$method = $this->_router->find($uri);
		$this->{$method}();
		
		return $this;
	}

	/**
	 * 
	 * @param Model_Page_Front $page
	 * @return \Behavior_Abstract
	 */
	public function set_page( Model_Page_Front &$page )
	{
		$this->_page = &$page;
		return $this;
	}
	
	/**
	 * 
	 * @return Model_Page_Front
	 */
	public function page()
	{
		return $this->_page;
	}
	
	/**
	 * 
	 * @return Behavior_Settings
	 */
	public function settings()
	{
		if($this->_settings === NULL)
		{
			$this->_settings = new Behavior_Settings($this->page());
		}
		
		return $this->_settings;
	}

	
	abstract public function execute();
}