<?php defined('SYSPATH') or die('No direct script access.');

Observer::observe('view_setting_plugins', function() {
	echo View::factory('search/settings');
});