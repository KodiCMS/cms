<?php defined('SYSPATH') or die('No direct script access.');

class Assets_Package {
	
	/**
	 * 
	 * @param string $name
	 * @param array $sources
	 * @return \Assets_Package
	 */
	public static function add($name, array $sources = array())
	{
		return new Assets_Package($name, $sources);
	}
	
	/**
	 *
	 * @var string 
	 */
	protected $_name;
	
	/**
	 *
	 * @var array 
	 */
	protected $_css = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_js = array();

	/**
	 * 
	 * @param string $name
	 * @param array $sources
	 */
	public function __construct($name, array $sources = array())
	{
		$this->_name = $name;
		
		foreach ($sources as $src)
		{
			if($src['type'] == 'js')
			{
				$this->_js[$src['handle']] = $src;
			}
			else
			{
				$this->_css[$src['handle']] = $src;
			}
		}
		
		Assets::$packages[$this->_name] = $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function css($handle = NULL, $src = NULL, $deps = NULL, $attrs = NULL)
	{
		if ($handle === NULL)
		{
			return $this->_css;
		}
		
		// Set default media attribute
		if ( ! isset($attrs['media']))
		{
			$attrs['media'] = 'all';
		}
		
		$this->_css[$handle] = array(
			'src'   => $src,
			'deps'  => (array) $deps,
			'attrs' => $attrs,
			'handle' => $handle,
			'type' => 'css'
		);
		
		return $this;
	}

	/**
	 * 
	 * @return array
	 */
	public function js($handle = FALSE, $src = NULL, $deps = NULL, $footer = FALSE)
	{
		if ($handle === TRUE OR $handle === FALSE)
		{
			return $this->_js;
		}
		
		$this->_js[$handle] = array(
			'src'    => $src,
			'deps'   => (array) $deps,
			'footer' => $footer,
			'handle' => $handle,
			'type' => 'js'
		);
		
		return $this;
	}
	
	public function __toString()
	{
		return (string) $this->_name;
	}
}