<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'Design',
		'children' => array(
			array(
				'divider' => TRUE,
				'name' => __('Widgets'), 
				'url' => Route::get('backend')->uri(array('controller'=>'widgets')),
				'permissions' => 'widgets.index',
				'priority' => 300,
				'icon' => 'th-large'
			),
		)
	)
);
