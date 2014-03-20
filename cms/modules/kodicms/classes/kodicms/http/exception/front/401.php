<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class KodiCMS_HTTP_Exception_Front_401 extends Kohana_HTTP_Exception_401 
{
	/**
	* Generate a Response for the 401 Exception.
	* 
	* The user should be redirect to a login page.
	* 
	* @return Response
	*/
	public function get_response()
	{
		Flash::set('protected_page', Context::instance()->get_page());

		$page = DB::select()
			->from('pages')
			->where('behavior_id', '=', 'protected_page')
			->limit(1)
			->as_object()
			->execute()
			->current();
	
		if($page)
		{
			$page = Model_Page_Front::find( $page->slug );

			// if we fund it, display it!
			if( is_object($page) )
			{				
				return Request::factory($page->url)
					->execute();
			}
		}
	}
}