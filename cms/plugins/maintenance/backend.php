<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Observer::observe('view_setting_plugins', function($plugin) {
	echo View::factory('maintenance/settings_page', array(
		'plugin' => $plugin
	));
}, $plugin);

Observer::observe('save_settings', function($post, $plugin) {

	if(!isset($post['plugin']['maintenance_mode']))
	{
		$plugin->set('maintenance_mode', 'no');
	}
	else
	{
		$plugin->set('maintenance_mode', 'yes');
	}
	
	$plugin->save_settings();
}, $plugin);