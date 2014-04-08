<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
final class Observer {

	static protected $events = array( );

	public static function observe( $event_names, $callback )
	{
		if ( !is_array( $event_names ) )
		{
			$event_names = array( $event_names );
		}
		
		$args = array_slice( func_get_args(), 2 );
		
		foreach ( $event_names as $event )
		{
			if ( !isset( self::$events[$event] ) )
			{
				self::$events[$event] = array();
			}
			
			self::$events[$event][] = array($callback, $args);
		}
	}

	public static function stopObserving( $event_name, $callback )
	{
		if ( isset( self::$events[$event_name][$callback] ) )
		{
			unset( self::$events[$event_name][$callback] );
		}
	}

	public static function clearObservers( $event_name )
	{
		self::$events[$event_name] = array( );
	}

	public static function getObserverList( $event_name )
	{
		return Arr::get(self::$events, $event_name, array());
	}

	/**
	 * If your event does not need to process the return values from any observers use this instead of getObserverList()
	 */
	public static function notify( $event_name )
	{
		$args = array_slice(func_get_args(), 1); // removing event name from the arguments

		foreach ( self::getObserverList( $event_name ) as $callback )
		{
			list($class, $class_args) = $callback;

			if(Kohana::$profiling === TRUE)
			{
				$benchmark = Profiler::start('Observer notify', $event_name);
			}

			if ( is_array( $class ) )
			{
				forward_static_call_array( $class, Arr::merge($args, $class_args) );
			}
			else
			{
				call_user_func_array( $class, Arr::merge($args, $class_args) );
			}

			if(isset($benchmark))
			{
				Profiler::stop($benchmark);
			}
		}
	}

}