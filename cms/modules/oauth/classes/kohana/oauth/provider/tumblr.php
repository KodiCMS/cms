<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Tumblr Provider
 *
 * Documents for implementing Tumblr OAuth can be found at
 * <http://www.tumblr.com/docs/en/api/v2>.
 *
 * [!!] This class does not implement the Tumblr API. It is only an
 * implementation of standard OAuth with Tumblr as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2011 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.1.4
 */
class Kohana_OAuth_Provider_Tumblr extends OAuth_Provider {

	public $name = 'tumblr';

	protected $signature = 'HMAC-SHA1';

	public function url_request_token()
	{
		return 'http://www.tumblr.com/oauth/request_token';
	}

	public function url_authorize()
	{
		return 'http://www.tumblr.com/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'http://www.tumblr.com/oauth/access_token';
	}

} // End Kohana_OAuth_Provider_Tumblr
