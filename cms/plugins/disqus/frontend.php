<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Observer::observe( 'disqus_comments', function($plugin) {
	echo View::factory( 'disqus/comments', array(
		'plugin' => $plugin
	));
}, $plugin);
	
Observer::observe( 'disqus_comments_counter', function($plugin) {
	echo View::factory( 'disqus/comments_counter', array(
		'plugin' => $plugin
	) );
}, $plugin);