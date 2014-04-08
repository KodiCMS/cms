<?php

abstract class Kohana_OAuth2_Provider_Google extends OAuth2_Provider {

	public $name = 'google';

	public function url_authorize()
	{
		return 'https://accounts.google.com/o/oauth2/auth';
	}

	public function url_access_token()
	{
		return 'https://accounts.google.com/o/oauth2/token';
	}

}