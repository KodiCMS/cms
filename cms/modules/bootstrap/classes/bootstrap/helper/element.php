<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Helper
 * @author		ButscHSter
 */
class Bootstrap_Helper_Element extends Bootstrap_Abstract {
	
	const PULL_LEFT = 'pull-left';
	const PULL_RIGHT = 'pull-right';

	/**
	 * 
	 * @return \Bootstrap_Abstract
	 */
	public function pull_left()
	{
		return $this->attributes('class', Bootstrap_Helper_Element::PULL_LEFT);
	}
	
	/**
	 * 
	 * @return \Bootstrap_Abstract
	 */
	public function pull_right()
	{
		return $this->attributes('class', Bootstrap_Helper_Element::PULL_RIGHT);
	}
}