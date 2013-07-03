<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Plugin_Redirect extends Plugin_Decorator {
	
	public function default_settings()
	{
		$settings = parent::default_settings();
		
		$settings['domain'] = $_SERVER['HTTP_HOST'];
		$settings['check_url_suffix'] = 'yes';

		return $settings;
	}
}