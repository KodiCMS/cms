<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Source_Document extends DataSource_Hybrid_Field_Source_OneToOne {

	protected $_props = array(
		'isreq' => TRUE,
		'ds_type' => NULL,
		'one_to_one' => FALSE
	);
	
	public function set( array $data )
	{
		$data['one_to_one'] = !empty($data['one_to_one']) ? TRUE : FALSE;
		return parent::set( $data );
	}
	
	public function create() 
	{
		parent::create();
		
		if( ! $this->id)
		{
			return FALSE;
		}

		$ds = Datasource_Data_Manager::load($this->from_ds);		
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
	
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new) 
	{
		if( $new->get($this->name) == -1 ) 
		{
			if($this->one_to_one) 
			{
				DataSource_Hybrid_Factory::remove_documents($old->get($this->name));
			}

			$new->set($this->name, NULL) ;
			return;
		}
	}
	
	public function onRemoveDocument( DataSource_Hybrid_Document $doc )
	{
		if($this->one_to_one) 
		{
			DataSource_Hybrid_Factory::remove_documents($doc->get($this->name));
		}
	}

	public function convert_value( $value ) 
	{
		$header = DataSource_Hybrid_Field_Utils::get_document_header($this->from_ds, $value);
		
		return array(
			'id' => $header ? $value : NULL,
			'header' => $header
		);
	}
	
	public function get_type()
	{
		return 'INT(11) UNSIGNED';
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

		if($recurse > 0 AND isset($widget->doc_fetched_widgets[$fid]))
		{
			$related_widget = self::_fetch_related_widget($widget, $row, $fid, $recurse);
		}

		return ($related_widget !== NULL) 
			? $related_widget 
			: $row[$fid];
	}
	
	public function fetch_headline_value( $value )
	{
		if(empty($value)) return parent::fetch_headline_value($value);

		$header = DataSource_Hybrid_Field_Utils::get_document_header($this->from_ds, $value);
		
		if(!empty($header))
		{
			return HTML::anchor(Route::get('datasources')->uri(array(
					'directory' => 'hybrid',
					'controller' => 'document',
					'action' => 'view'
				)) . URL::query(array('ds_id' => $this->from_ds, 'id' => $value), FALSE),
				$header,
				array(
					'class' => ' popup fancybox.iframe'
				)
			);
		}
		
		return parent::fetch_headline_value($value);
	}
	
	public function get_query_props(\Database_Query $query)
	{
		return $query->join(array('ds' . $this->ds_type, 'dss' . $this->id), 'left')
			->on(DataSource_Hybrid_Field::PREFFIX . $this->key, '=', 'dss' . $this->id . '.id')
			->on('dss' . $this->id . '.published', '=', DB::expr( 1 ))
			->select(array('dss'.$this->id.'.header', $this->id . 'header'));
	}
}