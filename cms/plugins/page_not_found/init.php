<?php defined('SYSPATH') or die('No direct access allowed.');

Plugin::factory('page_not_found', array(
	'title' => 'Page not found',
	'description' => 'Provides Page not found type.',
))->register();

Observer::observe('page_not_found', function( $message, $params ){
	throw new HTTP_Exception_Front_404($message, $params);
});
