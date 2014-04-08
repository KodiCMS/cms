<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Textarea extends Bootstrap_Form_Helper_Elements {
	
	protected $_template = 'form/element/textarea';
	
	public function required()
	{
		return array('name');
	}
	
	public function default_attributes()
	{
		return array(
			'rows' => 5
		);
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
	
	protected function _build_content() 
	{
		parent::_build_content();
		
		$this->_content = Form::textarea($this->get('name'), $this->get('body'), $this->attributes()->as_array());
	}
}