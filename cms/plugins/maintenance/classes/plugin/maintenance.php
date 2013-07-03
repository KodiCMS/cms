<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Plugin_Redirect extends Plugin_Decorator {
	
	public function default_settings()
	{
		$settings = parent::default_settings();
		
		$settings['maintenance_mode'] = 'no';
		return $settings;
	}
}