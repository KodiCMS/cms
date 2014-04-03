<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Driver
 * @author		ButscHSter
 */
abstract class Kohana_SSO_Driver_Oauth2_Google extends SSO_Driver_OAuth2 {

	protected $_provider = 'google';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$response = $user;
		$user = json_decode($user);
		$name = empty($user->name) ? trim($user->given_name . ' ' . $user->family_name) : $user->name;
		return array(
			'service_id'    => $user->id,
			'service_name'  => $name,
			'name'      => $name,
			'service_type'  => 'OAuth2.Google',
			'email'         => isset($user->email) ? $user->email : NULL, // may be empty
			'avatar'        => $user->picture ? $user->picture : '',
			'response'		=> $response
		);
	}

	protected function _url_verify_credentials(OAuth2_Token_Access $token)
	{
		return 'https://www.googleapis.com/oauth2/v1/userinfo';
	}
}