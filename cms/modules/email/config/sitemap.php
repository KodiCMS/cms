<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'System',
		'children' => array(
			array(
				'name' => 'Email',
				'icon' => 'envelope',
				'children' => array(
					array(
						'name' => __('Email templates'),
						'url' => Route::get('email_controllers')->uri(array('controller' => 'templates')),
						'permissions' => 'email_templates.index',
						'priority' => 400,
					),
					array(
						'name' => __('Email types'),
						'url' => Route::get('email_controllers')->uri(array('controller' => 'types')),
						'permissions' => 'email_types.index',
						'priority' => 410,
					)
				)
			)
		)
	)
);
