<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Navigation
 * @category	Model
 * @author		ButscHSter
 */
class Model_Navigation_Abstract {
	
	/**
	 *
	 * @var array
	 */
	protected $_params = array(
		'counter' => 0,
		'permissions' => NULL
	);
		
	/**
	 *
	 * @var Model_Navigation_Section 
	 */

	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		foreach ( $data as $key => $value )
		{
			$this->{$key} = $value;
		}
	}
	
	/**
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name )
	{
		return $this->get($name);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function get( $name, $default = NULL )
	{
		return Arr::get($this->_params, $name, $default);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Model_Navigation_Abstract
	 */
	public function __set( $name, $value )
	{
		$this->_params[$name] = $value;
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name) 
	{
		return isset($this->_params[$name]);
	}

	/**
	 * 
	 * @return boolean
	 */
	public function is_active()
	{
		return (bool) Arr::get($this->_params, 'is_active', FALSE);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function name()
	{
		return __(Arr::get($this->_params, 'name'));
	}
	
	/**
	 * 
	 * @return string
	 */
	public function url()
	{
		return Arr::get($this->_params, 'url');
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function counter()
	{
		return (int) Arr::get($this->_params, 'counter');
	}
	
	/**
	 * 
	 * @param boolean $status
	 * @return \Model_Navigation_Abstract
	 */
	public function set_active($status = TRUE)
	{
		$this->_params['is_active'] = (bool) $status;
		
		if($this->_section instanceof Model_Navigation_Section)
		{
			$this->_section->set_active($status);
		}

		return $this;
	}

	/**
	 * 
	 * @param Model_Navigation_Section $section
	 * @return \Model_Navigation_Page
	 */
	public function set_section( Model_Navigation_Section & $section)
	{
		$this->_section = $section;
		return $this;
	}
	
	/**
	 * 
	 * @return Model_Navigation_Section
	 */
	public function get_section()
	{
		return $this->_section;
	}
}