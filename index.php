<?php

$cms = 'cms' . DIRECTORY_SEPARATOR;

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @see  http://kohanaframework.org/guide/about.install#application
 */
$application = $cms . 'application';

/**
 * The directory in which your modules are located.
 *
 * @see  http://kohanaframework.org/guide/about.install#modules
 */
$modules = $cms . 'modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @see  http://kohanaframework.org/guide/about.install#system
 */
$system = $cms . 'system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the plugins relative to the docroot, for symlink'd index.php
if ( ! is_dir($cms) AND is_dir(DOCROOT.$cms))
	$cms = DOCROOT.$cms;

// Make the application relative to the docroot, for symlink'd index.php
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot, for symlink'd index.php
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot, for symlink'd index.php
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Define the absolute paths for configured directories
define('CMSPATH', realpath($cms).DIRECTORY_SEPARATOR);
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);
define('CFGFATH', DOCROOT.'config'.EXT);

// Clean up the configuration vars
unset($application, $modules, $system, $cms);

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Check is installed CMS
$is_installed = FALSE;
if(file_exists(CFGFATH))
{
	$is_installed = TRUE;
	include CFGFATH;
	
	if(
		! defined('ADMIN_DIR_NAME')
	)
	{
		$is_installed = FALSE;
	}
}

define('IS_INSTALLED', $is_installed);

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

$uri = TRUE;

if ( IS_INSTALLED )
{
	include APPPATH.'init'.EXT;
}
else if( is_dir( MODPATH . 'installer' ) )
{
	// Load the installation check
	include  MODPATH . 'installer' . DIRECTORY_SEPARATOR . 'bootstrap'.EXT;
}
else
{
	die('Please use module Installer to install KodiCMS.');
}

if (PHP_SAPI == 'cli') // Try and load minion
{
	class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
	set_exception_handler(array('Minion_Exception', 'handler'));

	Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{
	/**
	 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
	 * If no source is specified, the URI will be automatically detected.
	 */
	echo Request::factory($uri, array(), FALSE)
		->execute()
		->send_headers()
		->body();
}