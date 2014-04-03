<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('test', array(
	'title' => __('Test Site'),
	'version' => '1.0.0',
	'description' => 'DO NOT INSTALL TO PRODUCTION SERVER',
))->register();
