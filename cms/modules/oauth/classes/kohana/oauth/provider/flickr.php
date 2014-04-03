<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Flickr Provider
 *
 * Documents for implementing Flickr OAuth can be found at
 * <http://www.flickr.com/services/api/auth.oauth.html>.
 *
 * [!!] This class does not implement the Flickr API. It is only an
 * implementation of standard OAuth with Flickr as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2011 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.1.0
 */
class Kohana_OAuth_Provider_Flickr extends OAuth_Provider {

	public $name = 'flickr';

	protected $signature = 'HMAC-SHA1';

	public function url_request_token()
	{
		return 'http://www.flickr.com/services/oauth/request_token';
	}

	public function url_authorize()
	{
		return 'http://www.flickr.com/services/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'http://www.flickr.com/services/oauth/access_token';
	}

} // End OAuth_Provider_Flickr
