<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Primitive_Select extends DataSource_Hybrid_Field_Primitive {
	
	protected $_props = array(
		'options' => array(),
		'custom_option' => FALSE,
		'empty_value' => TRUE,
	);
	
	public function set( array $data )
	{
		$data['custom_option'] = !empty($data['custom_option']) ? TRUE : FALSE;
		$data['empty_value'] = !empty($data['empty_value']) ? TRUE : FALSE;	
		
		return parent::set( $data );
	}
	
	public function set_options( $options )
	{
		if( !is_array( $options ) )
		{
			$options = preg_split('/\\r\\n|\\r|\\n/', $options);
		}
		
		$options = array_unique(array_filter($options));
		$options = array_combine($options, $options);
		
		$this->options = (array) $options;
	}
	
	public function get_options()
	{
		if($this->empty_value === TRUE)
		{
			$this->options = array('--- Not set ---') + $this->options;
		}
		
		return $this->options;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return DataSource_Hybrid_Field
	 */
	public function set_document_value(array $data, DataSource_Hybrid_Document $document)
	{
		if($this->custom_option === TRUE AND isset($data[$this->name . '_custom']) AND !empty($data[$this->name . '_custom']))
		{
			$options = $this->options;
			$options[] = $data[$this->name . '_custom'];
			$this->set_options($options);
			$this->update();
		}
		
		return parent::set_document_value($data, $document);
	}

	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new) 
	{
		if( in_array($new->get($this->name), $this->options ) OR $this->custom_option === TRUE)
		{
			$new->set($this->name, $this->options[$new->get($this->name)]);
		}
		else if($new->get($this->name) == 0 AND $this->empty_value === TRUE)
		{
			$new->set($this->name, '');
		}
		else
		{
			$new->set($this->name, $old->get($this->name));
		}
	}
	
	public function document_validation_rules( Validation $validation, DataSource_Hybrid_Document $doc )
	{
		if($this->custom_option === FALSE)
		{
			if($this->empty_value === TRUE)
			{
				$this->options = array(0) + $this->options;
			}

			$validation->rule($this->name, 'in_array', array(':value', $this->options));
		}
			
		return parent::document_validation_rules($validation, $doc);
	}
	
	public function get_type() 
	{
		return 'TEXT NOT NULL';
	}
}