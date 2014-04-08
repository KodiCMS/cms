<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Behavior
 * @category	Model
 * @author		ButscHSter
 */
class Model_Page_Behavior_Setting extends ORM {
	
	protected $_primary_key = 'page_id';


	public function filters()
	{
		return array(
			'data' => array(
				array('serialize')
			)
		);
	}
	
	public function set_page( $page )
	{
		$this->_check_page($page);

		return $this
			->set('page_id', $page->id)
			->set('behavior_id', $page->behavior_id);
	}

	public function find_by_page( $page )
	{
		$this->_check_page($page);

		return $this->where('page_id', '=', $page->id)
			->find();
	}
	
	protected function _load_values(array $values)
	{
		if( ! empty($values['data']) )
		{
			$values['data'] = unserialize($values['data']);
		}
		
		parent::_load_values($values);
	}
	
	protected function _check_page($page)
	{
		if( $page instanceof Model_Page OR $page instanceof Model_Page_Front )
		{
			if( (int) $page->id == 0)
				throw new Kohana_Exception('Page must be loaded');
			
			return TRUE;
		}
		
		throw new Kohana_Exception('Page must be instanced of Model_Page OR Model_Page_Front');
	}
}