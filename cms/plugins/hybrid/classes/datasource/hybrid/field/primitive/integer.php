<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Primitive_Integer extends DataSource_Hybrid_Field_Primitive {
	
	protected $_use_as_document_id = TRUE;
	
	protected $_props = array(
		'default' => 0,
		'min' => 0, 
		'max' => 500,
		'length' => 10
	);
	
	public function set_value($value)
	{
		$value = (int) $value;

		if( ! empty($this->min) AND $value < $this->min )
		{
			$value = $this->min;
		}
		else if( ! empty($this->max) AND $value > $this->max )
		{
			$value = $this->max;
		}
		
		return $value;
	}
	
	public function set_default($value)
	{
		$this->default = $this->set_value($value);
	}
	
	public function set_min($value)
	{
		$this->min = (int) $value;
	}
	
	public function set_max($value)
	{
		$this->max = (int) $value;
	}
	
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new) 
	{
		$new->set($this->name, (int) $new->get($this->name));
	}
	
	public function document_validation_rules( Validation $validation, DataSource_Hybrid_Document $doc )
	{
		$validation->rule($this->name, 'numeric');
			
		return parent::document_validation_rules($validation, $doc);
	}
	
	public function get_type() 
	{
		if($this->length < 1 OR $this->length > 11)
		{
			$this->length = 10;
		}

		return 'INT(' . $this->length . ') UNSIGNED NOT NULL';
	}
	
	public static function fetch_widget_field( $widget, $field, $row, $fid, $recurse )
	{
		return (int) $row[$fid];
	}
}