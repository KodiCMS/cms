<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Oauth_Disqus extends Controller_Oauth2 {
	/**
	 * @var  OAuth2_Provider_Facebook
	 */
	protected $_provider;

	protected $_request_params = array(
		'scope'   => 'read',
	);

	public $name = 'disqus';
}
