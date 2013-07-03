<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Observer::observe('frontpage_requested', function($plugin) {
	$redirect = FALSE;
	$current_uri = $_SERVER['REQUEST_URI'];
	$path = $_SERVER['HTTP_HOST'] . $current_uri;
	$domain = $plugin->get('domain');

	if($_SERVER['HTTP_HOST'] != $domain) 
	{
		$redirect = TRUE;
		$path = $domain . $current_uri;
	}

	if($plugin->get( 'check_url_suffix') == 'yes') 
	{
		$current_uri = trim($current_uri, '/');

		if(
			strpos($path, URL_SUFFIX) === FALSE 
		AND 
			!empty($current_uri) 
		AND 
			$current_uri != 'index.php'
		) 
		{
			$redirect = TRUE;
			$path .= URL_SUFFIX;
		}
	}

	if($redirect === TRUE)
	{
		$protocol = Request::$initial->secure() ? 'https' : 'http';
		HTTP::redirect($protocol.'://' . $path, 301);
	}
}, $plugin );