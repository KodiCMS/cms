<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Archive
 * @author		ButscHSter
 */
class Model_Widget_Archive extends Model_Widget_Decorator {
	
	public $backend_template = 'archive';
	
	public $frontend_template = 'archive';
	
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
	
	public function get_page()
	{
		$page = NULL;
		if($this->page_id >= 1)
		{
			$page = Model_Page_Front::findById( $this->page_id );
		}
		else if( empty($this->page_id) )
		{
			$page = $this->_ctx->get_page();
		}
		
		return $page;
	}

	public function fetch_data()
	{
		return array();
	}
}