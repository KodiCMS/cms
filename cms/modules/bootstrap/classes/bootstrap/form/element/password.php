<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Password extends Bootstrap_Form_Element_Input {
	
	protected function _build_content() 
	{
		parent::_build_content();
		
		$this->_content = Form::password($this->get('name'), $this->get('value'), 
				$this->attributes()->as_array());
	}
}