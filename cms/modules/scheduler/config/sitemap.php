<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'Content',
		'children' => array(
			array(
				'name' => __('Scheduler'), 
				'url' => Route::get('backend')->uri(array('controller' => 'scheduler')),
				'priority' => 900,
				'icon' => 'calendar',
				'permissions' => 'scheduler.index'
			)
		)
	),
	array(
		'name' => 'System',
		'children' => array(
			array(
				'name' => __('Jobs'),
				'icon' => 'bolt',
				'url' => Route::get('backend')->uri(array('controller' => 'scheduler', 'action' => 'jobs')),
				'permissions' => 'scheduler.jobs',
				'priority' => 150,
			)
		)
	)
);