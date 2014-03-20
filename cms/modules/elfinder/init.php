<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Assets_Package::add('elfinder')
	->js('elfinder.lib', ADMIN_RESOURCES . 'libs/elfinder/js/elfinder.min.js', 'global')
	->js('elfinder.ru', ADMIN_RESOURCES . 'libs/elfinder/js/i18n/elfinder.ru.js', 'elfinder.lib')
	->js('elfinder', ADMIN_RESOURCES . 'js/elfinder.js', 'global')
	->css('elfinder.lib', ADMIN_RESOURCES . 'libs/elfinder/css/elfinder.min.css')
	->css('elfinder', ADMIN_RESOURCES . 'css/elfinder.css', 'elfinder.lib');