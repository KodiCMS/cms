<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'plugins' => array(
		array(
			'action' => 'index',
			'description' => 'View plugins'
		),
		array(
			'action' => 'settings',
			'description' => 'Edit plugin settings'
		),
		array(
			'action' => 'change_status',
			'description' => 'Install(Uninstall) plugins'
		),
	),
);