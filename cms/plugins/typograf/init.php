<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('typograf', array(
	'title' => __('Typographer Muraveva 3.0'),
	'version' => '3.0.0',
	'description' => 'Tool for formatting text using the rules, regulations and specifics of the Russian language and screen typography. It can connect to all text editors, faders and blogs.',
))->register();
