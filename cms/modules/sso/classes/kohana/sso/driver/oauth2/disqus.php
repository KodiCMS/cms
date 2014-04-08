<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Driver
 * @author		ButscHSter
 */
abstract class Kohana_SSO_Driver_OAuth2_Disqus extends SSO_Driver_OAuth2 {

	protected $_provider = 'disqus';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$response = $user;
		$user = json_decode($user);
		$user = $user->response;

		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->username,
			'name'			=> $user->name,
			'service_type'  => 'OAuth2.Disqus',
			'email'         => NULL,
			'avatar'        => $user->avatar->permalink,
			'response'		=> $response
		);
	}

	protected function _url_verify_credentials(OAuth2_Token_Access $token)
	{
		return 'https://disqus.com/api/3.0/users/details.json';
	}

	protected function _credential_params(OAuth2_Client $client, OAuth2_Token_Access $token)
	{
		return array(
			'access_token' => $token->token,
			'api_key' => $client->id,
			'api_secret' => $client->secret
		);
	}

}
