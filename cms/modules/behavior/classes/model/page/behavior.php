<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Behavior
 * @category	Model
 * @author		ButscHSter
 */
class Model_Page_Behavior extends Model_Page_Front {

	/**
	 * 
	 * @param string $part
	 * @param boolean $inherit
	 * @param integer $cache_lifetime
	 */
	public function content($part = 'body', $inherit = FALSE, $cache_lifetime = NULL, array $tags = array())
	{
		$method = 'content_' . URL::title($part, '_');
		if(method_exists($this, $method))
		{
			return $this->{$method}($cache_lifetime, $tags);
		}
		
		return parent::content($part, $inherit, $cache_lifetime, $tags);
	}
}