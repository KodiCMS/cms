<?php defined('SYSPATH') or die('No direct access allowed.');

Plugin::factory('less', array(
	'title' => 'LESS Compiler',
	'description' => 'LESS extends CSS with dynamic behavior such as variables, mixins, operations and functions.',
	'version' => '1.0.0',
))->register();