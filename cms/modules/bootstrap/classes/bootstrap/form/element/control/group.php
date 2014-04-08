<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Control_Group extends Bootstrap_Form_Helper_Elements {
	
	protected $_template = 'form/element/control/group';

	public function default_attributes()
	{
		return array(
			'class' => 'control-group'
		);
	}
}