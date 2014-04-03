<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Button extends Bootstrap_Element_Button {
	
	protected $_template = 'form/element/button';
	
	public function required()
	{
		return array('name', 'title');
	}
	
	/**
	 * 
	 * @param boolean $status
	 * @return Bootstrap_Form_Element_Button
	 */
	public function disabled()
	{
		return $this->attributes('disabled', 'disabled');
	}

	protected function _build_content() 
	{
		parent::_build_content();
		
		$this->_content = Form::button($this->get('name'), 
				$this->get('title'), $this->attributes()->as_array());
	}
}