<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'Documentation',
		'children' => array(
			array(
				'name' => __('User Guide'), 
				'url' => Route::get('backend')->uri(array('controller' => 'guide', 'action' => 'doc')),
				'priority' => 101,
				'icon' => 'book',
			),
			array(
				'name' => __('API Browser'), 
				'url' => Route::get('backend')->uri(array('controller' => 'guide', 'action' => 'api')),
				'priority' => 102,
				'icon' => 'beaker',
			)
		)
	)
);
