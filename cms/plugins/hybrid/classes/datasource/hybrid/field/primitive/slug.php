<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Primitive_Slug extends DataSource_Hybrid_Field_Primitive {

	protected $_use_as_document_id = TRUE;
	
	protected $_props = array(
		'default' => NULL,
		'separator' => '-',
		'from_header' => FALSE,
		'unique' => FALSE
	);

	public function set( array $data )
	{
		$data['from_header'] = !empty($data['from_header']) ? TRUE : FALSE;
		$data['unique'] = !empty($data['unique']) ? TRUE : FALSE;

		return parent::set( $data );
	}
	
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new) 
	{
		$new->set($this->name, URL::title($new->get($this->name)));
	}
	
	public function document_validation_rules( Validation $validation, DataSource_Hybrid_Document $doc )
	{
		if( $this->unique === TRUE )
		{
			$validation->rule($this->name, array($this, 'check_unique'), array(':value', $doc));
		}
			
		return parent::document_validation_rules($validation, $doc);
	}
	
	public function check_unique($value, $doc) 
	{
		return ! (bool) DB::select($this->name)
			->from($this->ds_table)
			->where($this->name, '=', $value)
			->where('id', '!=', $doc->id)
			->limit(1)
			->execute()
			->count();
	}
	
	public function get_type() 
	{
		return 'VARCHAR (255) NOT NULL';
	}
}