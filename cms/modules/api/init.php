<?php defined('SYSPATH') or die('No direct script access.');

Route::set('api', 'api(/<directory>)-<controller>(.<action>)(/<id>)', array('directory' => '.*'))
	->filter(function($route, $params, $request) {
		if (strpos($params['directory'], 'Api') === FALSE)
		{
			$params['directory'] = 'Api/' . $params['directory'];
		}

		return $params;
	})
	->defaults(array(
		'directory' => 'api'
	));

if(ACL::check('system.api'))
{
	Observer::observe('view_setting_plugins', 'api_mode_settings_page');
	Observer::observe('validation_settings', 'api_mode_validation_settings');

	function api_mode_validation_settings( $validation, $filter ) {
		$filter
			->rule('api.mode', FALSE, Config::NO); // If value not set, set default = no

		$validation
			->rule('api.mode', 'in_array', array(':value', array(Config::NO, Config::YES)));
	}

	function api_mode_settings_page() {
		echo View::factory('api/settings');
	}
};