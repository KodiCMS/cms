<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Horizontal extends Bootstrap_Form {
	
	public function default_attributes()
	{
		$array = parent::default_attributes();
		
		$array['class'] = Bootstrap_Form::HORIZONTAL;
		return $array;
	}

	protected function _build_content() 
	{
		parent::_build_content();

		foreach ($this->_elements as & $element)
		{
			if( 
				! ($element instanceof Bootstrap_Form_Element_Control_Group)
				AND
				! ($element instanceof Bootstrap_Form_Element_Fieldset)
			)
			{
				$element = Bootstrap_Form_Element_Control_Group::factory()
					->set('element', $element);
			}
		}
	}
}