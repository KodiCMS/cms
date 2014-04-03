<?php defined('SYSPATH') or die('No direct access allowed.');

// Для проверки одноразовых ссылок
Route::set( 'reflink', 'reflink/<code>(<suffix>)', array(
	'code' => '[A-Za-z0-9]+',
	'suffix' => URL_SUFFIX
) )
	->defaults( array(
		'controller' => 'reflink',
		'action' => 'index'
	) );