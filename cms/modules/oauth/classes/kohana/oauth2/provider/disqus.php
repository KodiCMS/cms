<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_OAuth2_Provider_Disqus extends OAuth2_Provider {

	public $name = 'disqus';

	public function url_authorize()
	{
		return 'https://disqus.com/api/oauth/2.0/authorize/';
	}

	public function url_access_token()
	{
		return 'https://disqus.com/api/oauth/2.0/access_token/';
	}
}