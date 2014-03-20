<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
final class Flash
{
	const SESSION_KEY = 'framework_flash';
	/**
	 * Return specific variable from the flash. If value is not found NULL is
	 * returned
	 *
	 * @param string $var Variable name
	 * @return mixed
	 */
	public static function get($var, $default = NULL)
	{
		return Session::instance()->get_once($var, $default);
	}

	/**
	 * Add specific variable to the flash. This variable will be available on the
	 * next page unless removed with the removeVariable() or clear() method
	 *
	 * @param string $var Variable name
	 * @param mixed $value Variable value
	 * @return void
	 */
	public static function set($var, $value)
	{
		Session::instance()->set($var, $value);
	} // set

	/**
	 * Call this function to clear flash. Note that data that previous page
	 * stored will not be deleted - just the data that this page saved for
	 * the next page
	 *
	 * @param none
	 * @return void
	 */
	public static function clear( $var )
	{
		Session::instance()->delete( $var );
	}
}