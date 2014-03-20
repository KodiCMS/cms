<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'scheduler' => array(
		array(
			'action' => 'index',
			'description' => 'View scheduler'
		),
		array(
			'action' => 'jobs',
			'description' => 'View jobs'
		),
		array(
			'action' => 'add',
			'description' => 'Add job'
		),
		array(
			'action' => 'edit',
			'description' => 'Edit job'
		),
		array(
			'action' => 'delete',
			'description' => 'Delete job'
		),
		array(
			'action' => 'run',
			'description' => 'Run job'
		),
	),
);