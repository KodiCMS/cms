<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Snippet
 * @author		ButscHSter
 */
class Snippet {
	
	/**
	 * 
	 * @param string $snippet_name
	 * @param array $vars
	 * @param integer $cache_lifetime
	 * @param boolean $cache_by_uri
	 * @param array $tags
	 * @param boolean $i18n
	 * @return void
	 */
	public static function render($snippet_name, $vars = NULL, $cache_lifetime = NULL, $cache_by_uri = FALSE, array $tags = array(), $i18n = NULL)
	{
		$view = Snippet::get($snippet_name, $vars);
		
		if( $view === NULL )
		{
			return NULL;
		}
		
		$cache_key = self::_cache_key($snippet_name, $cache_by_uri);
		
		if( ! in_array($snippet_name, $tags))
		{
			$tags[] = $snippet_name;
		}

		if( 
			Kohana::$caching === TRUE
		AND
			$cache_lifetime !== NULL 
		AND 
			! Fragment::load( $cache_key, (int) $cache_lifetime, $i18n ))
		{
			echo $view;

			Fragment::save_with_tags((int) $cache_lifetime, $tags);
		}
		else if( $cache_lifetime === NULL )
		{
			echo $view;
		}
	}
	
	/**
	 * 
	 * @param string $snippet_name
	 * @param array $vars
	 * @return null|View_Front
	 */
	public static function get($snippet_name, $vars = NULL)
	{
		$snippet = new Model_File_Snippet($snippet_name);
		
		if( ! $snippet->is_exists() )
		{
			if(($found_file = $snippet->find_file()) !== FALSE)
			{
				$snippet = new Model_File_Snippet( $found_file );
			}
			else
			{
				return NULL;
			}
		}
		
		$view = View_Front::factory($snippet->get_file(), $vars);
		
		if(isset($view->page_object))
		{
			$view->page = $view->page_object;
		}
		
		return $view;
	}

	/**
	 * 
	 * @param string $snippet_hash
	 * @param boolean $cache_by_uri
	 * @return string
	 */
	protected static function _cache_key($snippet_hash, $cache_by_uri = FALSE)
	{
		if($cache_by_uri !== FALSE)
		{
			$snippet_hash .= '::';
			$snippet_hash .= md5(Request::current()->uri() . serialize(Request::current()->query()));
		}
		
		return 'Snippet::' . $snippet_hash;
	}
}