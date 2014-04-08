<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Driver
 * @author		ButscHSter
 */
abstract class Kohana_SSO_Driver_OAuth_Twitter extends SSO_Driver_OAuth {

	protected $_provider = 'twitter';

	protected function _url_verify_credentials(OAuth_Token_Access $token)
	{
		return 'https://api.twitter.com/1.1/account/verify_credentials.json';
	}


	/**
	 * @param   string  $user object (response from Twitter OAuth)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$response = $user;
		$user = json_decode($user);

		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->screen_name,
			'name'			=> $user->name,
			'service_type'  => 'OAuth.Twitter',
			'email'         => NULL,
			'avatar'        => $user->profile_image_url,
			'response'		=> $response
		);
	}
}