<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Driver
 * @author		ButscHSter
 */
abstract class Kohana_SSO_Driver_OAuth_Google extends SSO_Driver_OAuth {

	protected $_provider = 'google';

	protected function _url_verify_credentials(OAuth_Token_Access $token)
	{
		return 'http://www-opensocial.googleusercontent.com/api/people/@me/@self';
	}

	/**
	 * @param   string  $user object (response from OAuth provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$response = $user;
		$user = json_decode($user);
		$user = $user->entry;
		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->displayName,
			'name'		    => $user->displayName,
			'service_type'  => 'OAuth.Google',
			'email'         => isset($user->email) ? $user->email : NULL, // may be empty
			'avatar'        => $user->thumbnailUrl ? $user->thumbnailUrl : '',
			'response'		=> $response
		);

	}

}