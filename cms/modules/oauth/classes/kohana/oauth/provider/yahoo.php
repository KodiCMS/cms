<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Yahoo Provider
 *
 * Documents for implementing Yahoo OAuth can be found at
 * <http://developer.yahoo.com/oauth/guide/index.html>.
 *
 * [!!] This class does not implement the Yahoo API. It is only an
 * implementation of standard OAuth with Yahoo as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Provider_Yahoo extends OAuth_Provider {

	/**
	 * @var string  Provider name
	 */
	public $name = 'yahoo';

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
		return 'https://api.login.yahoo.com/oauth/v2/get_request_token';
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function url_authorize()
	{
		return 'https://api.login.yahoo.com/oauth/v2/request_auth';
	}

	/**
	 * Access token URL
	 *
	 * @return string
	 */
	public function url_access_token()
	{
		return 'https://api.login.yahoo.com/oauth/v2/get_token';
	}

} // End Kohana_OAuth_Provider_Yahoo
