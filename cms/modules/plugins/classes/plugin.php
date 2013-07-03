<?php defined('SYSPATH') OR die('No direct script access.');

class Plugin {

	/**
	 * 
	 * @param string $id
	 * @param array $info
	 * @return \Plugin_Decorator
	 * @throws Plugin_Exception
	 */
	public static function factory( $id, array $info )
	{
		$class = 'Plugin_' . $id;
		
		if( class_exists( $class ))
		{
			return new $class( $id, $info );
		}
		
		return new Plugin_Decorator( $id, $info );
	}
}