<?php defined('SYSPATH') or die('No direct access allowed.');

class Behavior_Documents extends Behavior_Abstract
{
	/**
	 * 
	 * @return array
	 */
	public function routes()
	{
		return array(
			'/<id>' => array(
				'regex' => array(
					'id' => '[0-9]+'
				),
				'method' => 'page_by_id'
			),
			'/<slug>' => array(
				'regex' => array(
					'slug' => '.*'
				),
				'method' => 'page_by_slug'
			)
		);
	}
	
	public function page_by_id()
	{
		if( ! $this->router()->param('id')) 
		{
			return FALSE;
		}
		
		return $this->execute();
	}
	
	public function page_by_slug()
	{
		if( ! $this->router()->param('slug')) 
		{
			return FALSE;
		}
		
		if(($page = Model_Page_Front::findBySlug($this->router()->param('slug'), $this->page())) === FALSE )
		{
            return $this->execute();
		}
		
		$this->_page = $page;
	}

	public function execute()
	{
		$page_id = $this->__get_item_page_id();

		if( empty($page_id) || $this->router()->matched_route() === NULL) 
		{
			return FALSE;
		}
		
		$this->_page = Model_Page_Front::findById($page_id);
	}
	
	/**
	 * 
	 * @return integer
	 */
	protected function __get_item_page_id()
	{
		return (int) $this->settings()->item_page_id;
	}
}