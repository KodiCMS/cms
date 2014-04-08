<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('archive', array(
	'title' => 'Archive',
	'description' => 'Provides an Archive pagetype behaving similar to a blog or news archive.'
))->register();