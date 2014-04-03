<?php defined('SYSPATH') OR die('No direct script access!'); 

/**
 * @package		KodiCMS/Breadcrumbs
 * @author		ButscHSter
 */
class Kohana_Breadcrumbs_Item {
	
	/**
	 *
	 * @var string 
	 */
	public $url = NULL;
	
	/**
	 *
	 * @var string
	 */
	public $name = '';
	
	/**
	 *
	 * @var boolean
	 */
	public $active = TRUE;
	
	/**
	 *
	 * @var string
	 */
	protected $_data = array();


	/**
	 * 
	 * @param boolean $urls
	 * @param string $name
	 * @param string $url
	 * @param boolean $active
	 * @throws Kohana_Exception
	 */
	public function __construct($name, $url = NULL, $active = FALSE, array $data = array())
	{
		if(empty($name))
		{
			throw new Kohana_Exception('Breadcrumbs: The breadcrumb name could not be empty!');
		}

		$this->name = $name;
		if( ! empty($url))
		{
			$this->_set_url($url);
		}

		$this->active = $active;
		
		$this->_data = $data;
	}
	
	/**
	 * 
	 * @param string $url
	 */
	protected function _set_url($url)
	{
		$this->url = $url;//URL::site($url);
		return $this;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function is_active()
	{
		return (bool) $this->active;
	}

	/**
	 * 
	 * @return string
	 */
	public function link()
	{
		return HTML::anchor($this->url, $this->name);
	}

	/**
	 * 
	 * @param string|array $key
	 * @param mixed $value
	 * @return \Kohana_Breadcrumbs_Item
	 */
	public function set($key, $value = NULL)
	{
		if( is_array( $key ))
			$this->_data = $key;
		else
			$this->_data[$key] = $value;

		return $this;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return mixed|NULL
	 */
	public function get($key)
	{
		return Arr::get($this->_data, $key);
	}
	
	/**
	 * 
	 * @param string $key
	 * @return midex|NULL
	 */
	public function __get( $key )
	{
		return $this->get( $key );
	}
}
