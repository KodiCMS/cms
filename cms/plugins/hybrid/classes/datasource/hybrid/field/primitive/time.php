<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Primitive_Time extends DataSource_Hybrid_Field_Primitive_Date {
	
	protected $_format = 'H:i:s';
	
	public function get_type() 
	{
		return 'TIME NOT NULL';
	}
}