<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'system' => array(
		array(
			'action' => 'email.settings',
			'description' => 'Change email settings'
		)
	),
	'email' => array(
		array(
			'action' => 'templates.index',
			'description' => 'View Email Templates'
		),
		array(
			'action' => 'templates.add',
			'description' => 'Add Email Templates'
		),
		array(
			'action' => 'templates.edit',
			'description' => 'Edit Email Templates'
		),
		array(
			'action' => 'templates.delete',
			'description' => 'Delete Email Templates'
		),
		array(
			'action' => 'types.index',
			'description' => 'View Email Types'
		),
		array(
			'action' => 'types.add',
			'description' => 'Add Email Types'
		),
		array(
			'action' => 'types.edit',
			'description' => 'Edit Email Types'
		),
		array(
			'action' => 'types.delete',
			'description' => 'Delete Email Types'
		),
	),
);