<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Assets_Package::add('ace')
	->js('ace-library', ADMIN_RESOURCES . 'libs/ace/src-min/ace.js', 'global')
	->js('ace', ADMIN_RESOURCES . 'js/ace.js', 'global');

WYSIWYG::add('ace');