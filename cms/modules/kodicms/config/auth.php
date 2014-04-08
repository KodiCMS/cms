<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'       => 'orm',
	'hash_method'  => 'sha256',
	'hash_key'     => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
	'lifetime'     => 1209600,
	'session_type' => Session::$default,
	'session_key'  => 'auth_user',
	
	'password_length' => 5,
);
