<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Route::set( 'archive', ADMIN_DIR_NAME . '/archive/<id>' , array(
	'id' => '[0-9]+',
	'controller' => 'archive',
	'action' => 'index',
) )
	->defaults( array(
		'controller' => 'archive',
		'action' => 'index',
	) );

$behaviors = array();
foreach (Kohana::$config->load('behaviors') as $key => $behavior)
{
	if(isset($behavior['link']))
	{
		$behaviors[] = $key;
	}
}

if(empty($behaviors))
{
	return;
}

if(ACL::check('page.index'))
{
	$pages = DB::select()
		->from('pages')
		->where('behavior_id', 'in', $behaviors)
		->cache_key( 'archive_section' )
		->cached()
		->as_object()
		->execute();

	$root_section = Model_Navigation::get_section('Archive', Model_Navigation::get_section('Content'));
	$root_section->icon = 'archive';

	foreach ($pages as $page) 
	{
		$root_section
			->add_page(new Model_Navigation_Page(array(
				'name' => $page->title, 
				'url' => Route::get('archive')->uri(array(
					'controller' => 'archive', 'id' => $page->id
				)),
				'permissions' => 'page.index',
			)), 999);
	}
}

Observer::observe(array('page_delete', 'page_edit_after_save'), function() {
	Cache::instance()->delete('Database::cache(archive_section)');
});