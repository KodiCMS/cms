<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Plugin_Less extends Plugin_Decorator {
	
	public function default_settings()
	{
		$settings = parent::default_settings();
		
		$settings['less_folder_path'] = 'media/less';
		$settings['css_folder_path'] = 'media/css';
		$settings['enabled'] = Config::NO;
		
		return $settings;
	}
	
	public function set_settings( array $data )
	{
		if( !isset($data['enabled']) ) $data['enabled'] = Config::NO;

		return parent::set_settings($data);
	}
	
	public function is_dir_less()
	{
		return is_dir( $this->less_path());
	}
	
	public function is_dir_css()
	{
		return is_dir( $this->css_path());
	}
	
	public function less_path()
	{
		return DOCROOT . trim($this->less_folder_path, '/') . DIRECTORY_SEPARATOR;
	}
	
	public function css_path()
	{
		return DOCROOT . trim($this->css_folder_path, '/') . DIRECTORY_SEPARATOR;
	}
}