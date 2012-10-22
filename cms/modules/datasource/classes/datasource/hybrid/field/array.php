<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field_Array extends DataSource_Hybrid_Field_Document {

	protected $_props = array(
		'isreq' => TRUE
	);
	
	public function __construct( $data )
	{
		parent::__construct( $data );
		
		$this->family = self::TYPE_ARRAY;
	}
	
	public function create() 
	{
		parent::create();
		
		if( ! $this->id)
		{
			return FALSE;
		}
		
		$ds = DataSource_Hybrid_Field_Utils::load_ds($this->from_ds);
		$ds->increase_lock();
		
		$this->update();
		
		return $this->id;
	}
	
	public function remove() 
	{
		$ds = DataSource_Hybrid_Field_Utils::load_ds($this->from_ds);
		$ds->decrease_lock();

		parent::remove();
	}
	
	public function onUpdateDocument($old, $new) 
	{
		$o = empty($old->fields[$this->name]) ? array() : explode(',', $old->fields[$this->name]);
		$n = empty($new->fields[$this->name]) ? array() : explode(',', $new->fields[$this->name]);
		$diff = array_diff($o, $n);
		if(sizeof($diff) > 0)
		{
			$ds = $this->get_ds();
			$ds->delete($diff);
		}
	}
	
	public function fetch_value($doc) 
	{
		$ids = $doc->fields[$this->name] 
			? explode(',', $doc->fields[$this->name]) 
			: array();

		$doc->fields[$this->name] = DataSource_Hybrid_Field_Utils::get_document_headers($this->type, $this->from_ds, $ids);
	}
	
	public function convert_to_plain($doc) 
	{
		if(is_array($doc->fields[$this->name]))
		{
			$doc->fields[$this->name] = implode(', ', $doc->fields[$this->name]);
		}
	}
	
	public function is_valid($value) 
	{
		return strlen($value) == strspn($value, '0123456789,');
	}
	
	public function get_type()
	{
		return 'VARCHAR(255)';
	}
}