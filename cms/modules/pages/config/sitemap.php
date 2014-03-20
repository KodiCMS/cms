<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'Content',
		'children' => array(
			array(
				'name' => __('Pages'),
				'url' => URL::backend('page'),
				'permissions' => 'page.index',
				'priority' => 100,
				'icon' => 'sitemap'
			)
		)
	),
);
