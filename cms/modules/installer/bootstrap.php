<?php defined('SYSPATH') or die('No direct access allowed.');

if( ! defined('DB_TYPE') ) define('DB_TYPE', 'mysql');
if( ! defined('DB_SERVER') ) define('DB_SERVER', '');
if( ! defined('DB_NAME') ) define('DB_NAME', '');
if( ! defined('DB_USER') ) define('DB_USER', '');
if( ! defined('DB_PASS') ) define('DB_PASS', '');
if( ! defined('TABLE_PREFIX') ) define('TABLE_PREFIX', '');
if( ! defined('ADMIN_DIR_NAME') ) define('ADMIN_DIR_NAME', '');
if( ! defined('PUBLIC_URL') ) define('PUBLIC_URL', BASE_URL . 'public/');
if( ! defined('PLUGINS_URL') ) define('PLUGINS_URL', BASE_URL . 'cms/plugins/');
if( ! defined('IS_BACKEND') ) define('IS_BACKEND', FALSE);

/**
 * Set the default cookie salt
 */
Cookie::$salt = 'install_system';

/**
 * Disable kohana caching
 */
Kohana::$caching = FALSE;

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules( array(
	'api'			=> MODPATH . 'api',
	'users'			=> MODPATH . 'users',
	'kodicms'		=> MODPATH . 'kodicms',		// Core
	'assets'		=> MODPATH . 'assets',		// Asset Manager
	'cache'			=> MODPATH . 'cache',		// Cache manager
	'database'		=> MODPATH . 'database',	// Database access
	'auth'			=> MODPATH . 'auth',		// Basic authentication
	'orm'			=> MODPATH . 'orm',			// Object Relationship Mapping,
	'minion'		=> MODPATH . 'minion',		// Minion
	'filesystem'	=> MODPATH . 'filesystem',
	'bootstrap'		=> MODPATH . 'bootstrap',
	'breadcrumbs'	=> MODPATH . 'breadcrumbs',
	'widget'		=> MODPATH . 'widget',
	'email'			=> MODPATH . 'email',
	'installer'		=> MODPATH . 'installer'
) );

/**
 * Проверка на существование модуля `installer`
 */
if( array_key_exists('installer', Kohana::modules()) === FALSE )
{
	throw HTTP_Exception::factory(404, __('System not installed. Installer not found.'));
}

if (PHP_SAPI != 'cli')
{
	if( ! URL::match('install', Request::detect_uri()) )
	{
		$uri = Route::get('install')->uri();
	}
}