<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

if($plugin->get('maintenance_mode') == Config::YES)
{
	// Observe
	Observer::observe('frontpage_requested', function() {
		$page = DB::select()
			->from('pages')
			->where('behavior_id', '=', 'maintenance_mode')
			->limit(1)
			->as_object()
			->execute()
			->current();

		if ($page)
		{

			$page = Model_Page_Front::find( $page->slug );

			// if we fund it, display it!
			if( is_object($page) )
			{
				echo Response::factory()
					->status(403)
					->body($page->render_layout());

				exit(); // need to exit here otherwise the true error page will be sended
			}
		} 
		else 
		{
			throw new HTTP_Exception_403( 'Maintenance mode' );
			exit();
		}
	});
}