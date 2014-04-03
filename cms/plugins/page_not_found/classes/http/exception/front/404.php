<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class HTTP_Exception_Front_404 extends Kohana_HTTP_Exception_404 
{
	public function get_response()
	{
		$page_slug = DB::select('slug')
			->from('pages')
			->where('behavior_id', '=', 'page_not_found')
			->limit(1)
			->execute()
			->get('slug');
		
		if( ! empty($page_slug) )
		{
			$page = Model_Page_Front::find( $page_slug );

			if( $page instanceof Model_Page_Front )
			{
				return Request::factory($page->url)
					->query('message', $this->message)
					->execute();
			}
		}
		
		throw new HTTP_Exception_404('Something went wrong');
	}
}