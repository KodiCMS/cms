<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Hybrid_Record {
	
	/**
	 * Объект раздела
	 * @var DataSource_Section_Hybrid
	 */
	protected $_datasource;
	
	/**
	 * Идентификатор раздела
	 * @var integer
	 */
	protected $_ds_id;
	
	/**
	 * Поля раздела
	 * 
	 * @see DataSource_Hybrid_Record::fields()
	 * 
	 * @var array array([Field name] => DataSource_Hybrid_Field, ....)
	 */
	protected $_fields = NULL;
	
	/**
	 * 
	 * @param Datasource_Document $datasource
	 */
	public function __construct( Datasource_Section $datasource)
	{
		$this->_datasource = $datasource;
		$this->_ds_id = (int) $datasource->id();

		$this->load();
	}
	
	/**
	 * Получение идентификатора раздела
	 * @return integer
	 */
	public function ds_id()
	{
		return $this->_ds_id;
	}

	/**
	 * Загрузка полей раздела из БД
	 * 
	 * @return \DataSource_Hybrid_Record
	 */
	public function load() 
	{
		$this->_fields = array();
		
		$fields = DataSource_Hybrid_Field_Factory::get_section_fields($this->ds_id());
		
		foreach ($fields as $field)
		{
			$this->_fields[$field->name] = $field;
		}
		
		return $this;
	}
	
	/**
	 * Получение списка полей раздела
	 * 
	 * @return array array([Field name] => DataSource_Hybrid_Field, ....)
	 */
	public function fields()
	{
		if($this->_fields === NULL)
		{
			$this->load();
		}

		return $this->_fields;
	}

	/**
	 * Удаление полей раздела
	 * 
	 * @see DataSource_Section_Hybrid::remove()
	 * 
	 * @return \DataSource_Hybrid_Record
	 */
	public function destroy() 
	{
		DataSource_Hybrid_Field_Factory::remove_fields($this, array_keys($this->fields()));
		return $this;
	}
	
	/**
	 * Запуск события onCreateDocument для полей документа раздела
	 * 
	 * @see DataSource_Section_Hybrid::create_document()
	 * 
	 * @param DataSource_Hybrid_Document $document
	 * @return \DataSource_Hybrid_Record
	 */
	public function initialize_document( $document ) 
	{
		foreach($this->fields() as $field)
		{
			$field->onCreateDocument( $document );
		}
		
		return $this;
	}
	
	/**
	 * Запуск события onUpdateDocument для полей документа раздела
	 * 
	 * @see DataSource_Section_Hybrid::update_document()
	 * 
	 * @param DataSource_Hybrid_Document $old
	 * @param DataSource_Hybrid_Document $new
	 * @return \DataSource_Hybrid_Record
	 */
	public function document_changed( $old, $new ) 
	{
		foreach($this->fields() as $field)
		{
			$field->onUpdateDocument( $old, $new );
		}
		
		return $this;
	}
	
	/**
	 * Запуск события onRemoveDocument для полей документа раздела
	 * 
	 * @param DataSource_Hybrid_Document $document
	 * @return boolean
	 */
	public function destroy_document( $document ) 
	{
		if($document->ds_id != $this->ds_id())
		{
			return FALSE;
		}
		
		foreach($this->fields() as $field)
		{
			$field->onRemoveDocument( $document );
		}

		return TRUE;
	}
	
	/**
	 * Формирование запроса на обновление значений полей документа
	 * 
	 * Вызывает метод в полях документа get_sql
	 * 
	 * @param DataSource_Hybrid_Document $document
	 * @return array Массив SQL запросов
	 */
	public function get_sql( $document ) 
	{
		$queries = array();

		foreach($this->fields() as $field)
		{
			if($part = $field->get_sql( $document ))
			{
				$queries[$field->ds_table][$part[0]] = $part[1];
			}
		}
		
		$date_field = $document->loaded() ? 'updated_on' : 'created_on';
		
		$updates = array(
			(string) DB::update('dshybrid')
				->set(array(
					'published' => $document->published ? 1 : 0,
					'header' => $document->header,
					$date_field => date('Y-m-d H:i:s')
				))
				->where('id', '=', $document->id));

		foreach($queries as $table => $update)
		{
			$updates[] = (string) DB::update ( $table )
				->set($update)
				->where('id', '=', $document->id);
		}

		return $updates;
	}
}