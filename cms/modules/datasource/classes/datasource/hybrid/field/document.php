<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field_Document extends DataSource_Hybrid_Field {

	protected $_props = array(
		'isreq' => TRUE
	);
	
	public function __construct( $data )
	{
		parent::__construct( $data );
		
		$this->family = self::TYPE_DOCUMENT;
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
	
	
	
	public function update() 
	{
		return DB::update($this->table)
			->set(array(
				'header' => $this->header,
				'props' => serialize($this->_props),
				'from_ds' => $this->from_ds
			))
			->where('id', '=', $this->id)
			->execute();
	}
	
	public function remove() 
	{
		$ds = DataSource_Hybrid_Field_Utils::load_ds($this->from_ds);
		$ds->decrease_lock();

		parent::remove();
	}
	
	public function onUpdateDocument($old, $new) 
	{
		if($new->fields[$this->name] < 0 AND !$this->isreq) 
		{
			if($this->is_valid($old->fields[$this->name])) 
			{
				$ds = $this->get_ds();
				$ds->delete($old->fields[$this->name]);
			}

			$new->fields[$this->name] = NULL;
			return;
		}

		if(!$this->is_valid($new->fields[$this->name]))
		{
			$new->fields[$this->name] = $old->fields[$this->name];
		}
	}
	
	public function fetch_value($doc) 
	{
		$header = DataSource_Hybrid_Field_Utils::get_document_header($this->type, $this->from_ds, $doc->fields[$this->name]);
		$doc->fields[$this->name] = array(
			'id' => $header ? $doc->fields[$this->name] : NULL,
			'header' => $header
		);
	}
	
	public function convert_to_plain($doc) 
	{
		$doc->fields[$this->name] = Arr::path($doc->fields, $this->name . '.header');
	}
	
	public function is_valid($value) 
	{
		return $this->isreq ? $value > 0 : $value >= 0;
	}
	
	public function get_type()
	{
		switch($this->type) 
		{
			case Datasource_Manager::DS_HYBRID:
				return 'INT(11) UNSIGNED';
		}

		return NULL;
	}
}