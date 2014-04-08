<?php defined('SYSPATH') or die('No direct script access.');

define('INSTALL_DATA', MODPATH . 'installer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR);

if(array_key_exists(Arr::get($_GET, 'lang'), I18n::available_langs())) 
{
	I18n::lang(Arr::get($_GET, 'lang'));
}
else
{
	I18n::lang(I18n::detect_lang());
}

try 
{
	date_default_timezone_get();
} 
catch (Exception $e) 
{
	date_default_timezone_set('UTC');
}

Database::$default = 'install';

Route::set( 'install', 'install(/<action>(/<id>))' )
	->defaults( array(
		'controller' => 'install',
		'action' => 'error',
	) );