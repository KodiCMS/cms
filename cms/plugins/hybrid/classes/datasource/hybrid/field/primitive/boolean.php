<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Primitive_Boolean extends DataSource_Hybrid_Field_Primitive {
	
	protected $_props = array(
		'default' => FALSE
	);
	
	protected $_is_required = FALSE;
	
	public function set_default($value)
	{
		$this->default = (bool) $value; 
	}
	
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new) 
	{
		$new->set($this->name, $new->get($this->name) ? 1 : 0);
	}
	
	public function get_type() 
	{
		return 'TINYINT(1) UNSIGNED NOT NULL';
	}

	public static function fetch_widget_field( $widget, $field, $row, $fid, $recurse )
	{
		return (bool) $row[$fid];
	}
	
	public function fetch_headline_value( $value )
	{
		return $value == 1 ? __('TRUE') : __('FALSE');
	}
}