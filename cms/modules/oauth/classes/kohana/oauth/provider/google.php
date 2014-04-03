<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Google Provider
 *
 * Documents for implementing Google OAuth can be found at
 * <http://code.google.com/apis/accounts/docs/OAuth.html>.
 * Individual Google APIs have separate documentation. A complete list is
 * available at <http://code.google.com/more/>.
 *
 * [!!] This class does not implement any Google API. It is only an
 * implementation of standard OAuth with Google as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Provider_Google extends OAuth_Provider {

	public $name = 'google';

	protected $signature = 'HMAC-SHA1';

	public function url_request_token()
	{
		return 'https://www.google.com/accounts/OAuthGetRequestToken';
	}

	public function url_authorize()
	{
		return 'https://www.google.com/accounts/OAuthAuthorizeToken';
	}

	public function url_access_token()
	{
		return 'https://www.google.com/accounts/OAuthGetAccessToken';
	}

	public function request_token(OAuth_Consumer $consumer, array $params = NULL)
	{
		if ( ! isset($params['scope']))
		{
			// All request tokens must specify the data scope to access
			// http://code.google.com/apis/accounts/docs/OAuth.html#prepScope
			throw new Kohana_OAuth_Exception('Required parameter to not passed: :param', array(
				':param' => 'scope',
			));
		}

		return parent::request_token($consumer, $params);
	}

} // End OAuth_Provider_Google
