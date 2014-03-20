<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Abstract {

	/**
	 *
	 * @var string 
	 */
	protected $_template = 'default';
	
	/**
	 *
	 * @var View 
	 */
	protected $_view;

	/**
	 *
	 * @var array 
	 */
	protected $_options = array();

	/**
	 *
	 * @var array 
	 */
	protected $_attributes = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_field = array (
		'value' => NULL
	);
	
	protected $_inline = FALSE;

	/**
	 * 
	 * @param array $field
	 * @param boolean $render
	 * @throws Kohana_Exception
	 */
	public function __construct(array $field = array(), $render = TRUE) 
	{
		if(isset($field['inline']))
		{
			$this->_inline = (bool) $field['inline'];
		}

		$this->_attributes['id'] = Text::random('alpha');

		foreach($field as $key => $value) 
		{
			if(in_array($key, $this->_options))
			{
				$this->_field[$key] = $value;
			}
			else
			{
				$this->_attributes[$key] = $value;
			}
		}
		
		if( ! isset($this->_field['name']) )
		{
			throw new Kohana_Exception('Missing attribute "name" in field :field_name', array(
				':field_name' => substr(get_class($this), 15)));
		}

		$this->_view = View::factory('sidebar/fields/' . $this->_template);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->render();
	}

	/**
	 * 
	 * @return string
	 */
	public function render() 
	{
		foreach($this->_field as $name => $option)
		{
			if( ! in_array($name, $this->_options))
			{
				continue;
			}
			
			$this->_view->set($name, $option);
		}
		
		return (string) $this->_view
			->set('inline', $this->_inline)
			->set('attributes', $this->_attributes);
	}
}