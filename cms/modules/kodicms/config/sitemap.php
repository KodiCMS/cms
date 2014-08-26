<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => __('Dashboard'),
		'icon' => 'dashboard',
		'url' => Route::get('backend')->uri()
	),
	array(
		'name' => 'System',
		'icon' => 'cog',
		'priority' => 99,
		'children' => array(
			array(
				'name' => 'Information',
				'url' => Route::get('backend')->uri(array('controller' => 'system', 'action' => 'information')),
				'permissions' => 'system.information',
				'priority' => 90,
				'icon' => 'info-circle',
			),
			array(
				'name' => 'Settings',
				'url' => Route::get('backend')->uri(array('controller' => 'system', 'action' => 'settings')),
				'permissions' => 'system.settings',
				'priority' => 100,
				'icon' => 'cog',
			)
		)
		
	),
	array(
		'name' => 'Design',
		'icon' => 'desktop',
		'priority' => 4,
		'children' => array(
			array(
				'name' => 'Layouts', 
				'url' => Route::get('backend')->uri(array('controller' => 'layout')),
				'permissions' => 'layout.index',
				'priority' => 100,
				'icon' => 'desktop'
			)
		)
	),
);
