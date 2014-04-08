<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Redirect extends Controller_System_Plugin
{
	
	protected function _settings_save( $plugin )
	{
		if(!isset($_POST['setting']['check_url_suffix']))
		{
			$_POST['setting']['check_url_suffix'] = Config::NO;
		}

		parent::_settings_save($plugin);
	}

}