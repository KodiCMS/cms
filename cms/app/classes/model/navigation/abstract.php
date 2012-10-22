<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package    Kodi/Navigation
 */

class Model_Navigation_Abstract {
	
	/**
	 *
	 * @var array
	 */
	protected $params = array();
	

	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		foreach ( $data as $key => $value )
		{
			$this->params[$key] = $value;
		}
	}

	/**
	 * 
	 * @return boolean
	 */
	public function is_active()
	{
		return Arr::get($this->params, 'is_active', FALSE);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function name()
	{
		return __(Arr::get($this->params, 'name'));
	}
	
	/**
	 * 
	 * @return string
	 */
	public function url()
	{
		return Arr::get($this->params, 'url');
	}
	
	/**
	 * 
	 * @param boolean $status
	 * @return \Model_Navigation_Abstract
	 */
	public function set_active($status = TRUE)
	{
		$this->params['is_active'] = (bool) $status;
		
		return $this;
	}
}