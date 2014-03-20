<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Behavior
 * @author		ButscHSter
 */
class Behavior
{
	/**
	 *
	 * @var array 
	 */
	private static $behaviors = array();
	
	/**
	 * 
	 * @param string $behavior_id
	 * @return Behavior_Abstract
	 * @throws HTTP_Exception_404
	 */
	public static function factory($behavior_id)
	{
		$behavior = self::get($behavior_id);
		if( $behavior === NULL ) 
			throw new HTTP_Exception_404('Behavior :behavior not found!', array(
				':behavior' => $behavior_id
			));
		
		$class = $behavior_id;
		if(isset($behavior['class']))
		{
			$class = $behavior['class'];
		}
		
		$behavior_class = 'Behavior_'.URL::title($class, '');
		
		if ( ! class_exists($behavior_class) ) 
			throw new HTTP_Exception_404('Behavior class :class not exists!', array(
				':class' => $behavior_class
			));
		
		return new $behavior_class;
	}

	/**
	 * Init behaviors
	 */
	public static function init()
	{
		$config = Kohana::$config->load('behaviors');

		
		foreach ( $config as $behavior_id => $data )
		{
			self::$behaviors[$behavior_id] = $data;
		}
	}

	/**
	 * 
	 * @param type $behavior_id
	 * @return array
	 */
	public static function get($behavior_id)
	{
		return Arr::get(self::$behaviors, $behavior_id);
	}

	/**
	 * Load a behavior and return it
	 *
	 * @param behavior_id string  The Behavior plugin folder name
	 * @param page        object  Will be pass to the behavior
	 * 
	 *
	 * @return Behavior_Abstract
	 */
	public static function load($behavior_id, Model_Page_Front &$page, $url, $uri)
	{
		$behavior = self::factory($behavior_id);
		
		$uri = substr($uri, strlen($url));
		
		return $behavior
				->set_page($page)
				->execute_uri( $uri );
	}

	/**
	 * Load a behavior and return it
	 *
	 * @param behavior_id string  The Behavior plugin folder name
	 *
	 * @return string   class name of the page
	 */
	public static function load_page($behavior_id)
	{
		$behavior_page_class = 'Model_Page_Behavior_' . URL::title($behavior_id, '');

		if (class_exists($behavior_page_class))
		{
			return $behavior_page_class;
		}
		else
		{
			return 'Model_Page_Behavior';
		}
	}

	/**
	 *
	 * Find all active Behaviors id
	 * @return array
	 */
	public static function findAll()
	{
		return array_keys(self::$behaviors);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array|string $selected
	 * @param array $attributes
	 * @return string
	 */
	public static function select_choises()
	{
		$options = array('' => __('none'));
				
		foreach ( self::findAll() as $behavior )
		{
			$options[$behavior] = __(ucfirst(Inflector::humanize( $behavior )));
		}
		
		return $options;
	}
}