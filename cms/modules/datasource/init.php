<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set( 'datasources', ADMIN_DIR_NAME.'/<directory>(/<controller>(/<action>(/<id>)))', array(
	'directory' => '(datasources|' . implode('|', array_keys(Datasource_Data_Manager::types())) . ')'
))
	->defaults( array(
		'directory' => 'datasources',
		'controller' => 'data',
		'action' => 'index',
	) );

Observer::observe('modules::afer_load', function() {

	$types = Datasource_Data_Manager::types();
	
	if(empty($types))
	{
		return;
	}
	
	$ds_section = Model_Navigation::get_section('Datasources');
	$sections_list = Datasource_Data_Manager::get_tree(array_keys($types));

	foreach($sections_list as $type => $sections)
	{
		foreach ($sections as $id => $section)
		{
			$ds_section
				->add_page(new Model_Navigation_Page(array(
					'name' => $section['name'],
					'url' => Route::get('datasources')->uri(array(
						'controller' => 'data',
						'directory' => 'datasources',
					)) . URL::query(array('ds_id' => $id)),
					'icon' => 'folder-open-alt'
				)), 999);
		}
	}

	$section = Model_Navigation::get_section(__('Create section'), $ds_section);

	foreach ($types as $id => $type)
	{
		$section
			->add_page(new Model_Navigation_Page(array(
			'name' => $type,
			'url' => Route::get('datasources')->uri(array(
				'controller' => 'section',
				'directory' => 'datasources',
				'action' => 'create',
				'id' => $id
			)),
			'permissions' => $id.'.section.create'
		)));
	}
});

Observer::observe('update_search_index', function() {
	
	$ds_ids = Datasource_Data_Manager::get_all();
	
	foreach ($ds_ids as $ds_id => $data)
	{
		$ds = Datasource_Data_Manager::load($ds_id);
		
		if(! $ds->loaded()) continue;
		
		$ds->update_index();
	}
});