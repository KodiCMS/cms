<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Oauth_Google extends Controller_Oauth2 {

	/**
	 * @var  OAuth2_Provider_Google
	 */
	protected $_provider;

	protected $_request_params = array(
		'scope'   => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
	);

	public $name = 'google';
}