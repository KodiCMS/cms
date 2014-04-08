<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Fieldset extends Bootstrap_Form_Helper_Elements {

	protected $_template = 'form/element/fieldset';
	
	public function required()
	{
		return array('title');
	}
	
	protected function _build_content() 
	{
		parent::_build_content();
		
		if( $this->parent() instanceof Bootstrap_Form_Horizontal )
		{
			foreach ($this->_elements as & $element)
			{
				if( ! ($element instanceof Bootstrap_Form_Element_Control_Group))
				{
					$element = Bootstrap_Form_Element_Control_Group::factory()
						->set('element', $element);
				}
			}
		}
	}
}