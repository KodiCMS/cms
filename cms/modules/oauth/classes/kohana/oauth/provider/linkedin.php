<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Linkedin Provider
 *
 * Documents for implementing Linkedin OAuth can be found at
 * <http://developer.linkedin.com/docs/DOC-1251>.
 *
 * [!!] This class does not implement the Linkedin API. It is only an
 * implementation of standard OAuth with Linkedin as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Provider_Linkedin extends OAuth_Provider {

	/**
	 * @var string  Provider name
	 */
	public $name = 'linkedin';

	/**
	 * @var string  Signature
	 */
	protected $signature = 'HMAC-SHA1';

	/**
	 * Request token URL
	 *
	 * @return string
	 */
	public function url_request_token()
	{
		return 'https://api.linkedin.com/uas/oauth/requestToken';
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function url_authorize()
	{
		return 'https://api.linkedin.com/uas/oauth/authorize';
	}

	/**
	 * Access token URL
	 *
	 * @return string
	 */
	public function url_access_token()
	{
		return 'https://api.linkedin.com/uas/oauth/accessToken';
	}

} // End Kohana_OAuth_Provider_Linkedin
