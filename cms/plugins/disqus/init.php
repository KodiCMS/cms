<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('disqus', array(
	'title' => 'Disqus',
	'description' => 'Disqus is a global comment system that improves discussion on websites and connects conversations across the web.',
	'version' => '1.0.0',
))->register();