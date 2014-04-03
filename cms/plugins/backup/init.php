<?php defined('SYSPATH') or die('No direct access allowed.');

Plugin::factory('backup', array(
	'title' => 'Backup DB',
	'description' => 'Provides an Archive pagetype behaving similar to a blog or news archive.'
))->register();