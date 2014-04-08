<?php defined('SYSPATH') or die('No direct access allowed.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
* Set the default time zone.
*
* @link http://kohanaframework.org/guide/using.configuration
* @link http://www.php.net/manual/timezones
*/
// date_default_timezone_set('UTC');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale( LC_ALL, 'en_US.utf-8' );

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
//spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');


/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */

// -- Configuration and initialization -----------------------------------------

if ( isset( $_SERVER['KOHANA_ENV'] ) )
{
	Kohana::$environment = constant( 'Kohana::' . strtoupper( $_SERVER['KOHANA_ENV'] ) );
}
else if(IS_INSTALLED)
{
	Kohana::$environment = Kohana::PRODUCTION;
	
	// Turn off notices and strict errors
    error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
}


/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}


/**
 * InitializeCore, setting the default options.
 *
 * The following options are available:
 *
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init( array(
	'base_url'			=> '/kodicms/',
	'index_file'		=> FALSE,
	'cache_dir'			=> CMSPATH.'cache',
	'caching'			=> Kohana::$environment === Kohana::PRODUCTION,
	'profile'			=> Kohana::$environment !== Kohana::PRODUCTION,
	'errors'			=> TRUE
) );

define('CMS_NAME',			'Nerret Technology');
define('CMS_SITE',			'http://www.nerret-tech.com');
define('CMS_VERSION',		'NT-9.16.52');

define('PUBLICPATH',		DOCROOT . 'public' . DIRECTORY_SEPARATOR);
define('TMPPATH',			PUBLICPATH . 'temp' . DIRECTORY_SEPARATOR);
define('LAYOUTS_SYSPATH',	DOCROOT . 'layouts' . DIRECTORY_SEPARATOR);
define('SNIPPETS_SYSPATH',	DOCROOT . 'snippets' . DIRECTORY_SEPARATOR);

if(PHP_SAPI != 'cli')
{
	define('BASE_URL',		URL::base('http'));
	define('SITE_HOST',		str_replace('www.', '', $_SERVER['HTTP_HOST']));
}

if( ! defined( 'BASE_URL' ) ) define('BASE_URL', '/kodicms/');
if( ! defined( 'SITE_HOST' ) ) define('SITE_HOST', 'test');

define('ADMIN_RESOURCES',	BASE_URL . 'cms/media/');

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(CMSPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

Route::set( 'error', 'system/error(/<code>(/<message>))', array(
		'message' => '.*',
		'code' => '[0-9]+'
	) )
	->defaults( array(
		'directory' => 'system',
		'controller' => 'error',
		'action' => 'index'
	) );

Route::set( 'admin_media', 'cms/media/<file>', array(
	'file' => '.*'
))
	->defaults( array(
		'directory' => 'system',
		'controller' => 'media',
		'action' => 'media',
	) );