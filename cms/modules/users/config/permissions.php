<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'users' => array(
		array(
			'action' => 'index',
			'description' => 'View users'
		),
		array(
			'action' => 'add',
			'description' => 'Add new users'
		),
		array(
			'action' => 'edit',
			'description' => 'Edit users'
		),
		array(
			'action' => 'view.permissions',
			'description' => 'View user permissions'
		),
		array(
			'action' => 'change_roles',
			'description' => 'Change user roles'
		),
		array(
			'action' => 'change_password',
			'description' => 'Change password'
		),
		array(
			'action' => 'delete',
			'description' => 'Delete users'
		),
	),
	'roles' => array(
		array(
			'action' => 'index',
			'description' => 'View roles'
		),
		array(
			'action' => 'add',
			'description' => 'Add new roles'
		),
		array(
			'action' => 'edit',
			'description' => 'Edit roles'
		),
		array(
			'action' => 'change_permissions',
			'description' => 'Change role permissions'
		),
		array(
			'action' => 'delete',
			'description' => 'Delete roles'
		),
	),
);