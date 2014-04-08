<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Oauth_Github extends Controller_Oauth2 {
	/**
	 * @var  OAuth2_Provider_Github
	 */
	protected $_provider;

	public $name = 'github';
}