<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Components
 * @author		ButscHSter
 */
class Bootstrap_Dropdown_Element extends Bootstrap_Helper_Elements {

	protected function _build_content() 
	{
		$this->_content = '<li'.$this->attributes().'>' . $this->get('element') . '</li>';
	}
}