<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set( 'datasources', ADMIN_DIR_NAME.'/<directory>(/<controller>(/<action>(/<id>)))', array(
	'directory' => '(datasources|'.Datasource_Manager::DS_HYBRID.')'
))
		->defaults( array(
			'directory' => 'datasources',
			'controller' => 'data',
			'action' => 'index',
		) );

Model_Navigation::add_section('Datasources', __('Data'),  'datasources/data', array('administrator', 'developer', 'editor'), 101);
//Model_Navigation::add_section('Datasources', __('Objects'),  'datasources/objects', array('administrator', 'developer'), 102);
//Model_Navigation::add_section('Datasources', __('Layouts'),  'datasources/layouts', array('administrator', 'developer'), 103);