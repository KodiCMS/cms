<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Oauth_Vk extends Controller_Oauth2 {
	/**
	 * @var  OAuth2_Provider_Vk
	 */
	protected $_provider;

	public $name = 'vk';
}
