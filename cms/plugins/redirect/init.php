<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('redirect', array(
	'title' => 'Redirect 301',
	'description' => 'Provides an redirect to domain',
))->register();