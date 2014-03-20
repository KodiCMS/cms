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
						'url' => Route::url('email_controllers', array('controller' => 'templates')),
						'permissions' => 'email.templates.index',
						'priority' => 400,
					),
					array(
						'name' => __('Email types'),
						'url' => Route::url('email_controllers', array('controller' => 'types')),
						'permissions' => 'email.types.index',
						'priority' => 410,
					)
				)
			)
		)
	)
);
