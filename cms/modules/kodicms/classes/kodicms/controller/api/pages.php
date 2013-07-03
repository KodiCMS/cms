<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

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
		}
	}
	
	public function get_search()
	{
		$query = trim( $this->param('search', NULL, TRUE) );
		
		$childrens = array( );

		if ( $query == '*' )
		{
			$childrens = Model_Page::findAll();
		}
		else if ( strlen( $query ) == 2 AND $query[0] == '.' )
		{
			$page_status = array(
				'd' => Model_Page::STATUS_DRAFT,
				'r' => Model_Page::STATUS_REVIEWED,
				'p' => Model_Page::STATUS_PUBLISHED,
				'h' => Model_Page::STATUS_HIDDEN
			);

			if ( isset( $page_status[$query[1]] ) )
			{
				$childrens = Model_Page::find( array( 
					'where' => array(
						array('page.status_id', '=', $page_status[$query[1]])
					)));
			}
		}
		else if ( substr( $query, 0, 1 ) == '-' )
		{
			$query = trim( substr( $query, 1 ) );
			
			$subreqest = DB::select('p.id')
				->from(array(Model_Page::tableName(), 'p'))
				->where('p.slug', '=', $query)
				->limit(1);
			$childrens = Model_Page::find( array( 
				'where' => array(array('page.parent_id', '=', $subreqest))
			));
		}
		else
		{
			$childrens = Model_Page::findAllLike( $query );
		}

		foreach ( $childrens as $index => $child )
		{
			$childrens[$index]->is_expanded = false;
			$childrens[$index]->has_children = false;
		}

		$this->response( (string) View::factory( 'page/children', array(
			'childrens' => $childrens,
			'level' => 0
		) ));
	}
}