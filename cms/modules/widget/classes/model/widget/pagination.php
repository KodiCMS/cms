<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Other
 * @author		ButscHSter
 */
class Model_Widget_Pagination extends Model_Widget_Decorator {
	
	protected $_data = array(
		'query_key' => 'page'
	);
	
	public $use_caching = FALSE;
	
	public function on_page_load() 
	{
		$this->widget = $this->_ctx->get_widget($this->related_widget_id);
		$this->pagination = Pagination::factory();
		
		if(!($this->widget instanceof Model_Widget_Decorator)) 
		{
			return FALSE;
		}
		
		$this->pagination->setup(array(
			'items_per_page' => $this->widget->list_size,
			'total_items' => $this->widget->count_total(),
			'current_page' => array(
				'source' => 'query_string',
				'key' => $this->query_key
			)
		));

		$this->widget->list_offset = (int) $this->pagination->offset;
	}
	
	public function backend_data()
	{
		$widgets = Widget_Manager::get_all_widgets();
		
		$select = array();
		foreach($widgets as $id => $widget)
		{
			$class = 'Model_Widget_' . $widget['type'];
			
			if(!class_exists($class)) continue;
			
			$class = new ReflectionClass($class);
			if( $class->isSubclassOf('Model_Widget_Decorator_Pagination') )
				$select[$id] = $widget['name'];
		}
		
		return array(
			'select' => $select
		);
	}
	
	public function fetch_data()
	{
		$data = array(
			'total_items' => $this->pagination->total_items,
			'items_per_page' => $this->pagination->items_per_page,
			'total_pages' => $this->pagination->total_pages,
			'current_page' => $this->pagination->current_page,
			'current_first_item' => $this->pagination->current_first_item,
			'current_last_item' => $this->pagination->current_last_item,
			'previous_page' => $this->pagination->previous_page,
			'next_page' => $this->pagination->next_page,
			'first_page' => $this->pagination->first_page,
			'last_page' => $this->pagination->last_page,
			'offset' => $this->pagination->offset,
			'pagination' => $this->pagination
		);
		
		return $data;
	}
	
	public function get_cache_id()
	{
		$key = '';
		if($this->widget instanceof Model_Widget_Decorator) 
		{
			$key = $this->widget->id . '::' . $this->widget->list_offset;
		}
		
		return 'Widget::' . $this->id . '::' . $key;
	}
}