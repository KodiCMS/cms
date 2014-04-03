<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'System',
		'children' => array(
			array(
				'name' => __('Backup'), 
				'url' => URL::backend('backup'),
				'priority' => 110,
				'icon' => 'medkit'
			)
		)
	)
);
