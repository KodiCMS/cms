<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Source_Free extends DataSource_Hybrid_Field_Source_OneToOne {

	protected $_props = array(
		'isreq' => TRUE,
		'inject_key' => 'ids'
	);
	
	protected $_widget_types = array();
	
	public function __construct( array $data )
	{
		parent::__construct( $data );
		$this->family = DataSource_Hybrid_Field::FAMILY_SOURCE;
	}
	
	public function set_inject_key($key)
	{
		if(empty($key))
		{
			$this->inject_key = 'ids';
			return;
		}
		
		$this->inject_key = URL::title($key, '_');
	}
	
	public function get_type()
	{
		return 'VARCHAR(255)';
	}
	
	/**
	 * @param Model_Widget_Hybrid
	 * @param array $field
	 * @param array $row
	 * @param string $fid
	 * @return mixed
	 */
	public static function fetch_widget_field( $widget, $field, $row, $fid, $recurse )
	{
		$related_widget = NULL;
		
		Context::instance()->set($field->inject_key, explode(',', $row[$fid]));

		if($recurse > 0 AND isset($widget->doc_fetched_widgets[$fid]))
		{
			if(!empty($row[$fid]))
			{
				$related_widget = self::_fetch_related_widget($widget, $row, $fid, $recurse, $field->inject_key, TRUE);
			}
		}

		return !empty($related_widget) 
			? $related_widget 
			: $row[$fid];
	}
}