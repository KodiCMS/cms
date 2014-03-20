<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
class KodiCMS_Page_Field
{	
	/**
	 *
	 * @var array 
	 */
	protected static $_fields = array();
	
	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $key
	 * @param string $default
	 * @param boolean $inherit
	 * @return string
	 */
	public static function get( Model_Page_Front $page, $key, $default = NULL, $inherit = FALSE) 
	{
		if (self::exists( $page, $key ))
		{
			return self::$_fields[$page->id()][$key];
		}
		else if ($inherit !== FALSE
				AND $page->parent() instanceof Model_Page_Front )
		{
			return self::get($page->parent(), $key, $default, $inherit);
		}
		
		return $default;
	}

	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $key
	 * @param boolean $inherit
	 * @return boolean
	 */
	public static function exists( Model_Page_Front $page, $key, $inherit = FALSE)
	{
		if(Arr::get(self::$_fields, $page->id()) === NULL)
		{
			self::$_fields[$page->id()] = ORM::factory('Page_Field')
				->get_by_page_id($page->id())
				->as_array('key', 'value');
		}
		
		if(isset(self::$_fields[$page->id()][$key]))
		{
			return TRUE;
		}

		else if($inherit !== FALSE 
				AND $page->parent() instanceof Model_Page_Front )
		{
			return self::exists( $page->parent(), $key, $inherit);
		}

		return FALSE;
	}
}