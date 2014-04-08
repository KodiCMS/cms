<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Driver
 * @author		ButscHSter
 */
abstract class Kohana_SSO_Driver_OAuth_LinkedIn extends SSO_Driver_OAuth {

	protected $_provider = 'linkedin';

	protected function _url_verify_credentials(OAuth_Token_Access $token)
	{
		// @link https://developer.linkedin.com/documents/profile-fields
		return 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name,date-of-birth,picture-url)';
	}

	/**
	 * @param   string  $user object (response from LinkedIn OAuth)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = (array)simplexml_load_string($user);

		$login = trim(Arr::get($user, 'first-name') . ' ' . Arr::get($user, 'last-name'));

		return array(
			'service_id'    => $user['id'],
			'service_name'  => $login,
			'name'			=> $login,
			'service_type'  => 'OAuth.Linkedin',
			'email'         => NULL,
			'avatar'        => Arr::get($user, 'picture-url'),
		);

	}

}
