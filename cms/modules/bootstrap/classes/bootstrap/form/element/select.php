<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Select extends Bootstrap_Form_Helper_Elements {
	
	protected $_template = 'form/element/select';
	
	public function required()
	{
		return array('name');
	}
	
	/**
	 * 
	 * @param string $title
	 * @param array $attributes
	 * @return Bootstrap_Form_Element_Input
	 */
	public function label( $title, array $attributes = array() )
	{
		$label = Bootstrap_Form_Element_Label::factory(array(
				'for' => $this->attributes('id'), 
				'title' => $title
			), $attributes)
			->set_parent( $this );
		
		return $this->set('label', $label);
	}
	
	/**
	 * 
	 * @param string|array $option
	 * @return Bootstrap_Form_Element_Select
	 */
	public function selected( $option )
	{
		return $this->set('selected', $option);
	}
	
	public function multiple()
	{
		return $this->attributes('multiple', 'multiple');
	}

	protected function _build_content() 
	{
		parent::_build_content();
		
		$this->_content = Form::select($this->get('name'), $this->get('options'), 
				$this->get('selected'), $this->attributes()->as_array());
	}
}