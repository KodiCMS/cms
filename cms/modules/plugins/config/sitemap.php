<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'System',
		'children' => array(
			array(
			'name' => __('Plugins'), 
			'url' => Route::get('backend')->uri(array('controller' => 'plugins', 'action' => 'index')),
			'priority' => 999,
			'divider' => TRUE,
			'icon' => 'puzzle-piece',
			'permissions' => 'plugins.index',
		)
		)
		
	)
);
