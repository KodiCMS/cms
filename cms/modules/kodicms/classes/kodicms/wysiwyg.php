<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
class KodiCMS_WYSIWYG {

	/**
	 *
	 * @var array
	 */
	public static $filters = array();
	
	/**
	 *
	 * @var array
	 */
	public static $plugins = array();

	/**
	 * Add a new filter
	 *
	 * @param filter_id string  The WYSIWYG plugin folder name
	 * @param file      string  The file where the WYSIWYG class is
	 */
	public static function add( $filter_id)
	{
		self::$filters[$filter_id] = Inflector::humanize($filter_id);
	}
	
	/**
	 * 
	 * @param string $name
	 */
	public static function plugin( $name )
	{
		self::$plugins[] = (string) $name;
	}

	/**
	 * Remove a filter
	 *
	 * @param filter_id string  The WYSIWYG plugin folder name
	 */
	public static function remove( $filter_id )
	{
		if ( isset( self::$filters[$filter_id] ) )
		{
			unset( self::$filters[$filter_id] );
		}
	}
	
	public static function load_filters()
	{
		foreach (self::$filters as $key => $filter)
		{
			Assets::package($key);
		}
		
		foreach (self::$plugins as $plugin)
		{
			Assets::package($plugin);
		}
	}

	/**
	 * Find all active filters id
	 *
	 * @return array
	 */
	public static function findAll()
	{
		return self::$filters;
	}

	/**
	 * Get a instance of a filter
	 *
	 * @param filter_id string  The WYSIWYG plugin folder name
	 *
	 * @return mixed   if founded an object, else FALSE
	 */
	public static function get( $filter_id )
	{
		if ( isset( self::$filters[$filter_id] ) )
		{
			if ( ! class_exists( $filter_id ) )
			{
				return FALSE;
			}

			return new $filter_id;
		}
		else
		{
			return FALSE;
		}
	}
}