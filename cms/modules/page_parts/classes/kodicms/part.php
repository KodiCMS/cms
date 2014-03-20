<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
class KodiCMS_Part
{	
	/**
	 *
	 * @var array 
	 */
	protected static $_parts = NULL;
	
	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $part
	 * @param boolean $inherit
	 * @return boolean
	 */
	public static function exists( Model_Page_Front $page, $part, $inherit = FALSE)
	{
		if(Arr::get(self::$_parts, $page->id()) === NULL)
		{
			self::$_parts[$page->id()] = self::_load_parts($page->id());
		}
		
		if(isset(self::$_parts[$page->id()][$part]))
		{
			return TRUE;
		}
		else if($inherit !== FALSE 
				AND $page->parent() instanceof Model_Page_Front )
		{
			return self::exists( $this->parent(), $part, TRUE);
		}

		return FALSE;
	}
	
	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $part
	 * @param boolean $inherit
	 * @param integer $cache_lifetime
	 * @return void
	 */
	public static function content( Model_Page_Front $page, $part = 'body', $inherit = FALSE, $cache_lifetime = NULL, array $tags = array())
	{		
		if (self::exists( $page, $part ))
		{
			$view = self::get( $page->id(), $part);
			
			if( $cache_lifetime !== NULL 
				AND ! Fragment::load( $page->id() . $part . Request::current()->uri(), (int) $cache_lifetime ))
			{
				echo $view;				

				Fragment::save_with_tags((int) $cache_lifetime, array('page_parts'));
			}
			else if($cache_lifetime === NULL)
			{
				echo $view;
			}
			
		}
		else if ($inherit !== FALSE
				AND $page->parent() instanceof Model_Page_Front )
		{
			self::content( $page->parent(), $part, TRUE, $cache_lifetime);
		}
	}

	/**
	 * 
	 * @param integer $page_id
	 * @param string $part
	 * @return View
	 */
	public static function get( $page, $part )
	{
		$html = NULL;
		
		$page_id = ($page instanceof Model_Page_Front) ? $page->id() : (int) $page;

		if( empty(self::$_parts[$page_id][$part]) ) return NULL;

		if( self::$_parts[$page_id][$part] instanceof Model_Page_Part )
		{
			$html = View_Front::factory()
				->render_html(self::$_parts[$page_id][$part]->content_html);
		}
		else if( self::$_parts[$page_id][$part] instanceof Kohana_View )
		{
			$html = self::$_parts[$page_id][$part]->render();
		}
		
		return $html;
	}
	
	/**
	 * @return array
	 */
	final private static function _load_parts($page_id)
	{
		return DB::select('name', 'content', 'content_html')
			->from(Model_Page_Part::tableName())
			->where('page_id', '=', $page_id)
			->cache_tags( array('page_parts') )
			->as_object('Model_Page_Part')
			->cached( (int) Config::get('cache', 'page_parts'))
			->execute()
			->as_array('name');	
	}

}