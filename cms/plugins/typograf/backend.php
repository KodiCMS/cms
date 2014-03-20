<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

$typograf = Assets_Package::add('typograf')
	->js('typograf', ADMIN_RESOURCES . 'js/typograf.js', 'jquery');

WYSIWYG::plugin($typograf);