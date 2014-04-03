<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	SSO
 * @author		ButscHSter
 */
class Model_Widget_Social_Auth extends Model_Widget_Social_Registration {
	
	public $backend_template = 'social_registration';
	
	public function get_url($provider)
	{
		return Route::get('accounts-auth')->uri(array(
			'directory' => 'oauth', 
			'controller' => $provider, 
			'action' => 'auth')
		);
	}
}