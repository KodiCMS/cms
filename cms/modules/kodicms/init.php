<?php defined('SYSPATH') or die('No direct access allowed.');

Assets_Package::add('backbone')
	->js('underscore', ADMIN_RESOURCES . 'libs/underscore-min.js', 'jquery')
	->js('backbone', ADMIN_RESOURCES . 'libs/backbone-min.js', 'underscore');

Assets_Package::add('jquery-ui')
	->js('jquery-ui', ADMIN_RESOURCES . 'libs/jquery-ui/js/jquery-ui.custom.min.js', 'jquery')
	->css('jquery-ui', ADMIN_RESOURCES . 'libs/jquery-ui/css/flick/jquery-ui-1.10.2.custom.css', 'jquery');