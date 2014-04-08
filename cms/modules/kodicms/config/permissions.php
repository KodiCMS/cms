<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'layout' => array(
		array(
			'action' => 'index',
			'description' => 'View layouts'
		),
		array(
			'action' => 'add',
			'description' => 'Add layout'
		),
		array(
			'action' => 'edit',
			'description' => 'Edit layout'
		),
		array(
			'action' => 'view',
			'description' => 'View layout'
		),
		array(
			'action' => 'delete',
			'description' => 'Delete layout'
		),
		array(
			'action' => 'rebuild',
			'description' => 'Rebuild block list'
		),
	),
	'system' => array(
		array(
			'action' => 'settings',
			'description' => 'View settings'
		),
		array(
			'action' => 'information',
			'description' => 'View system information'
		),
		array(
			'action' => 'phpinfo',
			'description' => 'View PHP info'
		),
	),
);