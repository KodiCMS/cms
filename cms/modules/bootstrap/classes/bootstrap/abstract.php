<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		Twitter Bootstrap
 * @author		ButscHSter
 */
class Bootstrap_Abstract {
	
	/**
	 * 
	 * @param array $data
	 * @param array $attributes
	 * @param string $template
	 * @return \Bootstrap_Abstract
	 */
	public static function factory(array $data = array(), 
			array $attributes = array(), $template = NULL)
	{
		$class = get_called_class();
		return new $class($data, $attributes, $template);
	}

	/**
	 *
	 * @var View 
	 */
	protected $_template = NULL;
	
	/**
	 *
	 * @var string 
	 */
	protected $_template_folder = 'bootstrap';

	/**
	 *
	 * @var string 
	 */
	protected $_content = '';
	
	/**
	 *
	 * @var Bootstrap_Helper_Parameters 
	 */
	protected $_data = array();
	
	/**
	 *
	 * @var Bootstrap_Helper_Attributes 
	 */
	protected $_attributes = array();

	/**
	 *
	 * @var \Bootstrap_Abstract 
	 */
	protected $_parent = NULL;
	
	/**
	 * 
	 * @param array $attributes
	 * @param string $template
	 */
	public function __construct(array $data = array(), array $attributes = array(), $template = NULL) 
	{
		$this->_data = Arr::merge($data, $this->defaults());
		$this->_data = new Bootstrap_Helper_Parameters($this, $this->_data, 
				$this->required());

		$attributes = Arr::merge($attributes, $this->default_attributes());
		$this->_attributes = new Bootstrap_Helper_Attributes($this);
		$this->attributes($attributes);

		$this->_set_view($template);
	}

	/**
	 * 
	 * @param Bootstrap_Abstract $element
	 * @return \Bootstrap_Abstract
	 */
	public function set_parent( Bootstrap_Abstract $element )
	{
		$this->_parent = $element;
		return $this;
	}
	
	/**
	 * 
	 * @return \Bootstrap_Abstract|NULL
	 */
	public function parent()
	{
		return $this->_parent;
	}

	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return \UI_Abstract
	 */
	public function set($key, $value)
	{
		$this->_data->set($key, $value);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = NULL)
	{
		return $this->_data->get($key, $default);
	}
	
	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value) 
	{
		$this->set($key, $value);
	}
	
	/**
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key) 
	{
		return $this->get($key);
	}
	
	/**
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key) 
	{
		return isset($this->_data->$key);
	}
	
	/**
	 * 
	 * @param string|array|NULL $key
	 * @param string|array $value
	 * @return \UI_Abstract
	 */
	public function attributes($key = NULL, $value = NULL)
	{
		if( $key === NULL AND $value === NULL)
		{
			return $this->_attributes;
		}
		else if( is_array($key) OR $value !== NULL )
		{
			$this->_attributes->set($key, $value);
		}
		else if($value === NULL)
		{
			return $this->_attributes->get($key);
		}

		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function data()
	{
		return $this->_data->as_array();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function defaults()
	{
		return array();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function required()
	{
		return array();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function default_attributes()
	{
		return array();
	}
	
	public function content()
	{
		$this->_build_content();

		return $this->_content;
	}

	/**
	 * 
	 * @return string
	 */
	public function render()
	{
		$this->content();

		if( $this->_template instanceof View)
		{
			$this->_content = $this->_template->set(array(
				'attributes' => $this->attributes(),
				'element' => $this,
				'content' => $this->_content,
				'data' => $this->data(),
			))->render();
		}
		
		return (string) $this->_content;
	}

	/**
	 * 
	 * @param string $template
	 * @return \Bootstrap_Abstract
	 */
	protected function _set_view( $template = NULL )
	{
		if( $template !== NULL )
		{
			$this->_template = $template;
		}

		if($this->_template !== NULL)
		{
			$this->_template = View::factory( $this->_template_folder . '/' . $this->_template);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString() 
	{
		return (string) $this->render();
	}

	protected function _build_content() {}
}