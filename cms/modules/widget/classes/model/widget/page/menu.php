<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Page
 * @author		ButscHSter
 */
class Model_Widget_Page_Menu extends Model_Widget_Decorator {
	
	public $exclude = array();
	
	public $cache_tags = array('pages');

	public function backend_data()
	{
		$pages = Model_Page_Sitemap::get(TRUE);
		
		$select = array('-');
		foreach($pages->flatten() as $page)
		{
			$uri = !empty($page['uri']) ? $page['uri'] : '/';
			$select[$page['id']] = $page['title'] . ' (' . $uri . ')';
		}
		
		return array(
			'select' => $select,
			'pages' => $pages->flatten(),
		);
	}
	
	public function set_values(array $data)
	{
		if( empty( $data['exclude'] ))
		{
			$this->exclude = array();
		}
		
		if( empty( $data['match_all_paths'] ))
		{
			$this->match_all_paths = 0;
		}
		
		if( empty( $data['include_hidden'] ))
		{
			$this->include_hidden = 0;
		}
		
		return parent::set_values($data);
	}

	public function fetch_data()
	{
		$pages = Model_Page_Sitemap::get( (bool) $this->include_hidden );
		
		if( ($page_id = $this->get_page_id()) !== NULL )
		{
			$pages->find($page_id);
		}

		$pages->exclude( $this->exclude );

		return array(
			'pages' => $pages->children()->as_array( $this->match_all_paths == 1 )
		);
	}
	
	public function get_page_id()
	{
		if($this->page_id > 1)
		{
			return $this->page_id;
		}
		else if($this->page_id == 0 AND ($page = $this->_ctx->get_page()) instanceof Model_Page_Front)
		{
			return $page->id;
		}
		
		return NULL;
	}

	public function get_cache_id()
	{
		return 'Widget::' . $this->id . '::' . Request::current()->uri();
	}
	
	public function clear_cache()
	{
		$this->clear_cache_by_tags();

		return $this;
	}
}