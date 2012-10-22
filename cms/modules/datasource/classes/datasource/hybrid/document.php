<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Hybrid_Document {
	
	/**
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 *
	 * @var integer
	 */
	public $ds_id;
	
	/**
	 *
	 * @var boolean
	 */
	public $published = FALSE;
	
	/**
	 *
	 * @var string
	 */
	public $header;
	
	/**
	 *
	 * @var array
	 */
	public $fields = array();
	
	/**
	 *
	 * @var array
	 */
	public $field_names = array();
	
	/**
	 *
	 * @var Datasource_Hybrid_Record
	 */
	public $record;
	
	/**
	 * 
	 * @param Datasource_Hybrid_Record $record
	 */
	public function __construct( Datasource_Hybrid_Record $record)
	{
		$this->record = $record;
		$this->ds_id = $record->ds_id;
		$this->fields = array(
			'id' => $this->id, 
			'header' => $this->header
		);
	
		$this->field_names = array_keys($this->record->fields);
		$this->reset(); 
	}
	
	/**
	 * 
	 * @param array $arr
	 * @return \Datasource_Hybrid_Document
	 */
	public function read_values($arr = NULL) 
	{
		if($arr === NULL)
		{
			return $this;
		}

		$this->id = (int) Arr::get($arr, 'id');
		$this->ds_id = (int) Arr::get($arr, 'ds_id');
		$this->published = Arr::get($arr, 'published') ? TRUE : FALSE;
		$this->header = Arr::get($arr, 'header');
		
		foreach($this->field_names as $k)
		{
			if(isset($arr[$k]))
			{
				$this->fields[$k] = $arr[$k];
			}
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $arr
	 * @return \Datasource_Hybrid_Document
	 */
	public function read_files($arr) 
	{
		for($i = 0, $l = sizeof($this->field_names); $i < $l; $i++) 
		{
			$k = $this->field_names[$i];
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Datasource_Hybrid_Document
	 */
	public function fetch_values() 
	{
		foreach ( $this->field_names as $key )
		{
			$this->record->fields[$key]->fetch_value($this);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Datasource_Hybrid_Document
	 */
	public function convert_to_plain() 
	{
		for($i = 0, $l = sizeof($this->field_names); $i < $l; $i++)
		{
			$this->record->fields[$this->field_names[$i]]->convert_to_plain($this);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Datasource_Hybrid_Document
	 */
	public function reset() 
	{
		for($i = 0, $l = sizeof($this->field_names); $i < $l; $i++)
		{
			$this->fields[$this->field_names[$i]] = NULL;
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $array
	 * @param string $errors_file
	 * @return boolean|Validation
	 */
	public function validate($array, $errors_file = 'validation')
	{
		$array = Validation::factory($array)
			->rules( 'header', array(
				array('not_empty')
			) )
			->label( 'id', __('ID') )
			->label('header', __('Header'));

		foreach ($this->record->fields as $name => $field)
		{
			$array
				->label($name, $field->header);
			
			if($field->isreq === TRUE)
			{
				$array->rule($name, 'not_empty');
			}
			
			if(!empty($field->regexp))
			{
				$array->rule($name, array('regex', array(':value', $field->regexp)));
			}
			
			if(!empty($field->min) AND !empty($field->max))
			{
				$array->rule($name, array('range', array(':value', $field->min, $field->max)));
			}
			
			switch($field->type) 
			{
				case DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_DATE: 
				case DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_DATETIME:
					$array->rule($name, 'date');
					break;
				case DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_EMAIL:
					$array->rule($name, 'email');
					break;
				case DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_INTEGER:
					$array->rule($name, 'digit');
					break;
				case DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_FLOAT:
					$array->rule($name, 'numeric');
			}
		}

		if(!$array->check())
		{
			return $array->errors($errors_file);
		}
		
		return TRUE;
	}
}