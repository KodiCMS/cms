<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class KodiCMS_Controller_API_Pages extends Controller_System_Api {
	
	public function get_get()
	{		
		$uids = $this->param('uids');
		$parent = $this->param('pid');
		
		$pages = Model_API::factory('api_page')
			->get_all($uids, $parent, $this->fields);

		$this->response($pages);
	}

	public function get_tags()
	{
		$uid = $this->param('uid', NULL, TRUE);
		
		$tags = Model_API::factory('api_page_tag')
			->get_all(NULL, $this->fields, $uid);
		
		$this->response($tags);
	}
	
	public function get_by_uri()
	{
		$uri = $this->param('uri', NULL, TRUE);

		$page = Model::factory('api_page')
			->find_by_uri($uri, $this->fields);
		
		$this->response($page);
	}
	
	public function get_sort()
	{
		$pages = Model_Page_Sitemap::get( TRUE )->as_array();

		$this->response((string) View::factory( 'page/sort', array(
			'pages' => $pages
		)));
	}
	
	public function post_sort()
	{
		$pages = $this->param('pages', array(), TRUE);
		
		if( count( $pages ) > 0)
		{
			$insert = DB::insert('pages')->columns(array('id', 'parent_id', 'position'));

			foreach ($pages as $page)
			{
				$insert->values(array((int) $page['id'], (int) $page['parent_id'], (int) $page['position']));
			}
			
			$insert = $insert . ' ON DUPLICATE KEY UPDATE parent_id = VALUES(parent_id), position = VALUES(position)';
		
			DB::query(Database::INSERT, $insert)->execute();
			
			if(Kohana::$caching === TRUE)
			{
				Cache::instance()->delete_tag('pages');
			}
		}
	}
	
	public function get_search()
	{
		$query = trim( $this->param('search', NULL, TRUE) );
		
		$pages = ORM::factory('page');

		if ( strlen( $query ) == 2 AND $query[0] == '.' )
		{
			$page_status = array(
				'd' => Model_Page::STATUS_DRAFT,
				's' => Model_Page::STATUS_PASSWORD_PROTECTED,
				'p' => Model_Page::STATUS_PUBLISHED,
				'h' => Model_Page::STATUS_HIDDEN
			);

			if ( isset( $page_status[$query[1]] ) )
			{
				$pages->where('status_id', '=', $page_status[$query[1]]);
			}
		}
		else
		{
			$pages->like( $query );
		}

		$childrens = array();
		$pages = $pages->find_all();

		foreach ( $pages as $page )
		{
			$page->is_expanded = FALSE;
			$page->has_children = FALSE;
			
			$childrens[] = $page;
		}

		$this->response( (string) View::factory( 'page/children', array(
			'childrens' => $childrens,
			'level' => 0
		) ));
	}
}