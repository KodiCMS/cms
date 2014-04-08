<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Archive
 * @author		ButscHSter
 */
class Model_Widget_Archive_HL extends Model_Widget_Page_Pages {
	
	public $backend_template = 'page_pages';

	public function fetch_data()
	{
		$page = $this->get_page();
		
		if( ! ($page instanceof Model_Page_Behavior_Archive) )
		{
			Model_Page_Front::not_found();
		}

		$params = $page->behavior()->router()->params();

		$date = implode('-', $params);
		
		$clause = array(
			'where' => array(array('page.published_on', 'like', $date . '%')),
			'order_by' => array(array('page.published_on', 'desc'))
		);
		
		if($this->list_offset > 0)
		{
			$clause['offset'] = (int) $this->list_offset;
		}
		
		if($this->list_size > 0)
		{
			$clause['limit'] = (int) $this->list_size;
		}

		$pages = $page->parent()->children($clause);

		return array(
			'pages' => $pages
		);
	}
}