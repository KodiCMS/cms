<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Hybrid_Document {
	
	/**
	 * Список полей документа
	 * @var array array([ID] => [Document value])
	 */
	protected $_fields = array();
	
	/**
	 * Список системных полей
	 * @var array 
	 */
	protected $_system_fields = array(
		'id' => NULL,
		'ds_id' => NULL,
		'published' => FALSE,
		'header' => NULL
	);
	
	/**
	 *
	 * @var array 
	 */
	protected $_changed_fields = array();

	/**
	 *
	 * @var DataSource_Hybrid_Record
	 */
	protected $_record;
	
	/**
	 * 
	 * @param DataSource_Hybrid_Record $record
	 */
	public function __construct( DataSource_Hybrid_Record $record )
	{
		$this->_record = $record;
		$this->_system_fields['ds_id'] = $record->ds_id();

		$this->reset(); 
	}
	
	/**
	 * Сеттер. Присваивает значение полю документа
	 * 
	 * @param string $field
	 * @param string $value
	 */
	public function __set($field, $value)
	{
		$this->set($field, $value);
	}
	
	/**
	 * Геттер значений полей документов
	 * 
	 * @param string $field
	 * @return mixed
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Проаверка существаования поля в документе
	 * 
	 * @param type $field
	 * @return type
	 */
	public function __isset($field)
	{
		return isset($this->_fields[$field]);
	}

	/**
	 * Проверка существования документа
	 * 
	 * @return boolean
	 */
	public function loaded()
	{
		return (int) $this->id != 0;
	}
	
	/**
	 * Геттер значений полей документов
	 * 
	 * @param string $field
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($field, $default = NULL)
	{
		if(isset($this->_fields[$field]))
		{
			return $this->_fields[$field];
		}
		else if(isset($this->_system_fields[$field]))
		{
			return $this->_system_fields[$field];
		}

		return NULL;
	}
	
	/**
	 * Сеттер. Присваивает значение полю документа
	 * 
	 * @param string $field
	 * @param string $value
	 */
	public function set($field, $value)
	{
		if(array_key_exists($field, $this->_system_fields))
		{
			if(($field == 'id' OR $field == 'ds_id' ) AND $this->loaded())
			{
				return $this;
			}

			$this->_system_fields[$field] = $this->_run_filter($field, $value);
			$this->_changed_fields[$field] = $this->_system_fields[$field];
		}
		else if(array_key_exists($field, $this->_fields))
		{
			$this->set_field_value($field, $value);
		}
		
		return $this;
	}

	/**
	 * Получение старого значения поля, до присвоения нового
	 * 
	 * @param string $field
	 * @param mixed $default
	 * @return mixed
	 */
	public function get_old_value($field, $default = NULL)
	{
		return Arr::get($this->_changed_fields, $field);
	}
	
	/**
	 * Проверка поля на изменение значения
	 * 
	 * @param string $field
	 * @return boolean
	 */
	public function is_changed( $field )
	{
		return $this->{$field} == $this->get_old_value($field);
	}

	/**
	 * 
	 * @return DataSource_Hybrid_Record
	 */
	public function record()
	{
		return $this->_record;
	}

	/**
	 * Правила фильтрации полей документа
	 * @return array
	 */
	public function filters()
	{
		return array(
			'id' => array(
				array('intval')
			),
			'ds_id' => array(
				array('intval')
			),
			'published' => array(
//				array('boolval')
			)
		);
	}
	
	/**
	 * Правила валидации полей документа
	 * @return type
	 */
	public function rules()
	{
		return array(
			'header' => array(
				array('not_empty')
			)
		);
	}
	
	/**
	 * Заголовки полей
	 * @return type
	 */
	public function labels()
	{
		return array(
			'id' => __('ID'),
			'header' =>  __('Header')
		);
	}
	
	/**
	 * Получение всех значений полей
	 * 
	 * @return array array([Field name] => [value])
	 */
	public function values()
	{
		return Arr::merge($this->_fields, $this->_system_fields);
	}

	/**
	 * Загрузка документа по его ID
	 * 
	 *		$ds = Datasource_Data_Manager::load($ds_id);
	 *		$doc = $ds->get_document($id);
	 * 
	 * @param integer $id
	 * @return \DataSource_Hybrid_Document
	 */
	public function load( $id )
	{
		$ds_id = $this->record()->ds_id();

		$result = DB::select(array('dshybrid.id', 'id'))
			->select('ds_id', 'published', 'header')
			->select_array( array_keys( $this->_fields ))
			->from('dshybrid')
			->join("dshybrid_{$ds_id}")
				->on("dshybrid_{$ds_id}.id", '=', 'dshybrid.id')
			->where('dshybrid.id', '=', (int) $id)
			->limit(1)
			->execute()
			->current();
		
		foreach($result as $field => $value)
		{
			$this->{$field} = $value;
		}
		
		return $this;
	}

	/**
	 * Загрузка данных из массива
	 * 
	 * @param array $array Массив значений полей документа
	 * @return \DataSource_Hybrid_Document
	 */
	public function read_values(array $array = NULL) 
	{
		foreach($this->record()->fields() as $field)
		{
			$field->set_document_value($array, $this);
		}
		
		foreach($this->_system_fields as $key => $value)
		{
			$this->{$key} = Arr::get($array, $key);
		}
		
		return $this;
	}
	
	/**
	 * Загрузка файлов из массива
	 * 
	 * @param array $array
	 * @return \DataSource_Hybrid_Document
	 */
	public function read_files($array) 
	{
		foreach($this->record()->fields() as $key => $field)
		{
			if(
				isset($array[$key]) 
			AND
				$field->family == DataSource_Hybrid_Field::FAMILY_FILE 
			AND 
				Upload::valid( $array[$key] ) 
			AND 
				Upload::not_empty($array[$key]))
			{
				$field->set_document_value($array, $this);
			}
		}
	
		return $this;
	}
	
	/**
	 * Установка значения поля документа (не системного)
	 * 
	 * 
	 * @param string $field
	 * @param mixed $value
	 */
	public function set_field_value($field, $value)
	{
		$this->_changed_fields[$field] = $this->_fields[$field];
		
		$fields = $this->record()->fields();
		
		$this->_fields[$field] = isset($fields[$field]) 
			? $fields[$field]->onSetValue( $value, $this )
			: $value;
	}
	
	/**
	 * Конвертация значений полей документа в момент загрузкти данных в форму
	 * редактора
	 * 
	 * @return \DataSource_Hybrid_Document
	 */
	public function convert_values() 
	{
		foreach( $this->record()->fields() as $key => $field )
		{
			$this->{$key} = $field->convert_value( $this->{$key} );
		}
		
		return $this;
	}
	
	/**
	 * Сброс значений полей документа
	 * 
	 * @return \DataSource_Hybrid_Document
	 */
	public function reset() 
	{
		foreach ($this->_system_fields as $key => $value)
		{
			$this->_system_fields[$key] = NULL;
		}

		foreach( $this->record()->fields() as $key => $field )
		{
			$this->_fields[$key] = NULL;
		}
		
		return $this;
	}
	
	/**
	 * Фильтрация полей документа согласно правилам
	 * 
	 * @see DataSource_Hybrid_Document::filters()
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @return mixed
	 */
	protected function _run_filter($field, $value)
	{
		$filters = $this->filters();

		// Get the filters for this column
		$wildcards = empty($filters[TRUE]) ? array() : $filters[TRUE];

		// Merge in the wildcards
		$filters = empty($filters[$field]) ? $wildcards : array_merge($wildcards, $filters[$field]);

		// Bind the field name and model so they can be used in the filter method
		$_bound = array
		(
			':field' => $field,
			':document' => $this,
		);

		foreach ($filters as $array)
		{
			// Value needs to be bound inside the loop so we are always using the
			// version that was modified by the filters that already ran
			$_bound[':value'] = $value;

			// Filters are defined as array($filter, $params)
			$filter = $array[0];
			$params = Arr::get($array, 1, array(':value'));

			foreach ($params as $key => $param)
			{
				if (is_string($param) AND array_key_exists($param, $_bound))
				{
					// Replace with bound value
					$params[$key] = $_bound[$param];
				}
			}

			if (is_array($filter) OR ! is_string($filter))
			{
				// This is either a callback as an array or a lambda
				$value = call_user_func_array($filter, $params);
			}
			elseif (strpos($filter, '::') === FALSE)
			{
				// Use a function call
				$function = new ReflectionFunction($filter);

				// Call $function($this[$field], $param, ...) with Reflection
				$value = $function->invokeArgs($params);
			}
			else
			{
				// Split the class and method of the rule
				list($class, $method) = explode('::', $filter, 2);

				// Use a static method call
				$method = new ReflectionMethod($class, $method);

				// Call $Class::$method($this[$field], $param, ...) with Reflection
				$value = $method->invokeArgs(NULL, $params);
			}
		}

		return $value;
	}

	/**
	 * Валидация полей документа согласно правилам валидации
	 * 
	 * @see DataSource_Hybrid_Document::rules()
	 * 
	 *			$doc = $ds->get_document($id);
	 *			$doc
	 *				->read_values($this->request->post())
	 *				->read_files($_FILES)
	 *				->validate($this->request->post() + $_FILES);
	 * 
	 * @param array $array
	 * @param string $errors_file
	 * @return boolean|Validation
	 */
	public function validate($errors_file = 'validation')
	{
		$validation = Validation::factory($this->values());
		
		foreach ($this->rules() as $field => $rules)
		{
			$validation->rules($field, $rules);
		}
		
		foreach ($this->labels() as $field => $label)
		{
			$validation->label($field, $label);
		}

		foreach ($this->record()->fields() as $name => $field)
		{
			$field->document_validation_rules($validation, $this);
		}

		if( ! $validation->check() )
		{
			throw new Validation_Exception( $validation );
		}
		
		return TRUE;
	}
	
	/**
	 * Метод удаления документа
	 * 
	 * @return null|boolean
	 */
	public function remove()
	{
		if( ! $this->loaded() ) return NULL;
		
		DB::delete("dshybrid_" . $this->ds_id)
			->where('id', '=', $this->id)
			->execute();
		
		$this->reset();
		
		return TRUE;
	}
}