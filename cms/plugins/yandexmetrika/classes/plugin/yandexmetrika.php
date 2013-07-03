<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Plugin_YandexMetrika extends Plugin_Decorator {
	
	public function default_settings()
	{
		$settings = parent::default_settings();
		
		$settings['webvisor'] = 0;
		$settings['clickmap'] = 1;
		$settings['track_links'] = 1;
		$settings['accurate_track_bounce'] = 1;
		
		return $settings;
	}
	
	public function set_settings( array $data )
	{
		if( !isset($data['webvisor']) ) $data['webvisor'] = 0;
		if( !isset($data['clickmap']) ) $data['clickmap'] = 0;
		if( !isset($data['track_links']) ) $data['track_links'] = 0;
		if( !isset($data['accurate_track_bounce']) ) $data['accurate_track_bounce'] = 0;
		
		$data['counter_id'] = (int)$data['counter_id'];
		
		return parent::set_settings($data);
	}
}