<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('tagsinput', array(
	'title' => 'jQuery Tags Input',
	'description' => 'Do you use tags to organize content on your site? This plugin will turn your boring tag list into a magical input that turns each tag into a style-able object with its own delete link. The plugin handles all the data - your form just sees a comma-delimited list of tags!',
))->register();