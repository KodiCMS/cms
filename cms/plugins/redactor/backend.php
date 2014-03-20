<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Assets_Package::add('redactor')
	->js('redactor.ru', PLUGINS_URL . $plugin->id() . '/vendors/redactor/ru.js', 'jquery')
	->js('redactor.min', PLUGINS_URL . $plugin->id() . '/vendors/redactor/redactor.js', 'jquery')
	->js('redactor', ADMIN_RESOURCES . 'js/redactor.js', 'global')
	->css('redator', PLUGINS_URL . $plugin->id() . '/vendors/redactor/redactor.css');

WYSIWYG::add('redactor');