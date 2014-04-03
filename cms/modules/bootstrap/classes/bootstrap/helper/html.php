<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Helper
 * @author		ButscHSter
 */
class Bootstrap_Helper_HTML extends Bootstrap_Helper_Element {

	public function required()
	{
		return array('string');
	}

	protected function _build_content() 
	{
		$this->_content = $this->get('string');
	}
}