<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Page
 * @author		ButscHSter
 */
class Model_Widget_Page_Search extends Model_Widget_Decorator_Pagination {
	
	public $cache_tags = array('pages');
	
	protected $_total = 0;
	
	public function keyword()
	{
		return HTML::chars($this->_ctx->get($this->search_key));
	}
	
	public function on_page_load()
	{
		$this->count_total();
	}

	public function fetch_data()
	{
		$keyword = $this->keyword();

		$return = array(
			'total_found' => 0,
			'results' => array(),
			'keyword' => $keyword
		);

		if(empty($keyword)) return $return;
		
		$ids = Search::instance()->find_by_keyword($keyword, FALSE, 'pages', $this->list_size, $this->list_offset);

		if(empty($ids['pages'])) return $return;
		
		$pages = array();
		foreach ($ids['pages'] as $item)
		{
			if(($page = Model_Page_Front::findById($item['id'])) === FALSE )
			{
				$this->_total--;
				continue;
			}
			
			$pages[$item['id']] = $page;
		}
		
		$return['total_found'] = $this->_total;
		$return['results'] = $pages;

		return $return;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function count_total()
	{
		$keyword = $this->_ctx->get($this->search_key);
		$this->_total = Search::instance()->count_by_keyword($keyword, FALSE, 'pages');
		
		return $this->_total;
	}
	
	public function get_cache_id()
	{
		return 'Widget::' . $this->type . '::' . $this->id . '::' . $this->keyword();
	}
}