<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Model_Widget_Hybrid extends Model_Widget_Decorator_Pagination {

	/**
	 * 
	 * @return array
	 */
	public function options()
	{
		$datasources = Datasource_Data_Manager::get_all('hybrid');
		
		$options = array(__('--- Not set ---'));
		foreach ($datasources as $value)
		{
			$options[$value['id']] = $value['name'];
		}

		return $options;
	}
	
	/**
	 * @param array array
	 * @return array
	 */
	public function get_related_widgets( array $types )
	{
		$db_widgets = Widget_Manager::get_widgets( $types );
		$widgets = array();
		foreach ($db_widgets as $id => $obj)
		{
			$widgets[$id] = $obj['name'];
		}

		return $widgets;
	}

	/**
	 *
	 * @var DataSource_Hybrid_Agent 
	 */
	protected $_agent = NULL;

	/**
	 *
	 * @var bool
	 */
	public $only_sub = FALSE;
	
	/**
	 *
	 * @var array 
	 */
	protected $_documents = array();

	/**
	 * 
	 * @return DataSource_Hybrid_Agent
	 */
	protected function get_agent()
	{
		if($this->_agent === NULL)
		{
			$this->_agent = DataSource_Hybrid_Agent::instance($this->ds_id, $this->ds_id, $this->only_sub);
		}
		
		return $this->_agent;
	}
}