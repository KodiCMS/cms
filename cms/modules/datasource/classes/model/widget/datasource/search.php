<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Page
 * @author		ButscHSter
 */
class Model_Widget_Datasource_Search extends Model_Widget_Decorator_Pagination {
	
	/**
	 *
	 * @var integer 
	 */
	protected $_total = 0;
	
	/**
	 *
	 * @var array 
	 */
	public $sources = array();
	
	/**
	 *
	 * @var array 
	 */
	public $source_hrefs = array();
	
	/**
	 *
	 * @var array 
	 */
	public $search_key = 'keyword';
	
	/**
	 * @param array $data
	 */
	public function set_values(array $data)
	{
		if(empty($data['sources']))
		{
			$data['sources'] = array();
		}
		
		return parent::set_values($data);
	}

	/**
	 * 
	 * @return string
	 */
	public function keyword()
	{
		return HTML::chars($this->_ctx->get($this->search_key));
	}
	
	public function on_page_load()
	{
		$this->count_total();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function sources()
	{
		$sources_list = Datasource_Data_Manager::get_all();
		
		$sources = array();

		foreach ($sources_list as $source)
		{
			$sources[$source['id']] = $source['name'];
		}

		return $sources;
	}

	/**
	 * 
	 * @return array
	 */
	public function modules()
	{
		$modules = array();

		foreach ($this->sources as $source_id)
		{
			$modules[] = 'ds_' . $source_id;
		}
		
		return $modules;
	}

	/**
	 *
	 * @var array 
	 */
	public function fetch_data()
	{
		$keyword = $this->keyword();

		$return = array(
			'total_found' => 0,
			'results' => array(),
			'keyword' => $keyword
		);

		$modules = $this->modules();

		$ids = Search::instance()->find_by_keyword($keyword, FALSE, $modules, $this->list_size, $this->list_offset);

		if(empty($ids))
		{
			return $return;
		}
		
		$results = array();
		
		foreach ($this->source_hrefs as $id => $href)
		{
			if( ! isset($ids['ds_' . $id])) continue;
	
			foreach ($ids['ds_' . $id] as $item)
			{
				$item['href'] = str_replace('$id', $item['id'], $href);
				
				$results[] = $item;
			}
		}
		
		$return['results'] = $results;
		$return['total_found'] = count($results);

		return $return;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function count_total()
	{
		$keyword = $this->keyword();
		$this->_total = Search::instance()->count_by_keyword($keyword, FALSE, $this->modules());

		return $this->_total;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function get_cache_id()
	{
		return 'Widget::' . $this->type . '::' . $this->id . '::' . $this->keyword();
	}
}