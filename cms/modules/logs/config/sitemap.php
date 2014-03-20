<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'System',
		'children' => array(
			array(
				'name' => __('Logs'),
				'icon' => 'time',
				'url' => Route::url('backend', array('controller' => 'logs')),
				'permissions' => 'logs.index',
				'priority' => 150,
			)
		)
	)
);
