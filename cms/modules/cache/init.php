<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Set the default cache driver
 */
Cache::$default = defined('CACHE_TYPE') ? CACHE_TYPE : 'file';

//Observer::observe('modules::afer_load', function() {
//	$driver = defined('CACHE_TYPE') ? CACHE_TYPE : 'file';
//	
//	if( Config::get('cache', 'driver') === NULL ) 
//	{
//		Config::set('cache', 'driver', $driver);
//	}
//
//	Cache::$default = Config::get('cache', 'driver');
//});

if(ACL::check('system.cache.settings'))
{
	Observer::observe('view_setting_plugins', function() {
		echo View::factory('cache/settings');
	});

	Observer::observe('validation_settings', function( $validation, $filter ) {
		$filter
			->rule('cache.front_page', 'intval')
			->rule('cache.page_parts', 'intval')
			->rule('cache.tags', 'intval');
	});
}