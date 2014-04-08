<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Page
 * @author		ButscHSter
 */
class Model_Widget_Page_Breadcrumbs extends Model_Widget_Decorator {
	
	public $exclude = array();
	
	public $cache_tags = array('pages');

	public function backend_data()
	{
		return array(
			'pages' => Model_Page_Sitemap::get(TRUE)->flatten(),
		);
	}
	
	public function set_values(array $data)
	{
		if( empty( $data['exclude'] ))
		{
			$this->exclude = array();
		}

		return parent::set_values($data);
	}

	public function fetch_data()
	{
		$crumbs = array();
		
		if( ($crumbs = &$this->_ctx->get_crumbs()) instanceof Breadcrumbs)
		{
			if( ! empty($this->exclude))
			{
				$crumbs->delete_by('id', $this->exclude);
			}
		}

		return array(
			'crumbs' => $crumbs
		);
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