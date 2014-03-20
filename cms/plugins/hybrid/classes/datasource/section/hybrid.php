<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Section_Hybrid extends Datasource_Section {
	
	/**
	 * Таблица раздела
	 * 
	 * @var string
	 */
	protected $_ds_table = 'dshybrid';
	
	/**
	 * Тип раздела
	 * 
	 * @var string
	 */
	protected $_type = 'hybrid';
	
	/**
	 * Индексируемы поля раздела
	 * 
	 * @var array 
	 */
	public $search_index_fields = array();
	
	/**
	 * Поле описания документа в поисковом индексе
	 * 
	 * @var integer 
	 */
	public $search_intro_field = NULL;
	
	/**
	 * Шаблон формы редактирования документа
	 * 
	 * @var string 
	 */
	public $template = NULL;

	/**
	 * 
	 * @var DataSource_Hybrid_Record
	 */
	protected $_record = NULL;
	
	/**
	 *
	 * @var DataSource_Hybrid_Agent 
	 */
	protected $_agent = NULL;
	
	/**
	 * 
	 * @return DataSource_Hybrid_Record
	 */
	public function record() 
	{
		if($this->_record === NULL)
		{
			$this->_record = new DataSource_Hybrid_Record($this);
		}

		return $this->_record;
	}
	
	/**
	 * 
	 * @return DataSource_Hybrid_Agent
	 */
	public function agent() 
	{
		if($this->_agent === NULL)
		{
			$this->_agent = DataSource_Hybrid_Agent::instance($this->id());
		}

		return $this->_agent;
	}
	
	/**
	 * Создание раздела
	 * 
	 * @param array $values
	 * @return integer Идентификатор раздела
	 */
	public function create( array $values )
	{
		$id = parent::create($values);
		
		DataSource_Hybrid_Factory::create($this);

		return $id;
	}
	
	/**
	 * Получение списка полей раздела
	 * 
	 * @return array array([Field ID] => [Field Header], ....)
	 */
	public function record_fields_array( )
	{
		$fields = array();

		foreach( $this->record()->fields() as $field)
		{
			$fields[$field->id] = $field->header;
		}
		
		return $fields;
	}
	
	/**
	 * Сохранение раздела
	 * 
	 * 
	 * @param array $values
	 * @return boolean
	 */
	public function save(array $values = NULL)
	{
		if( ! $this->loaded())
		{
			return FALSE;
		}
		
		$this->doc_order = Arr::get($values, 'doc_order', array());
		$this->template = empty($values['template']) ? NULL : $values['template'];
		
		$this->search_intro_field = empty($values['search_intro_field']) ? NULL : $values['search_intro_field'];
		unset($values['search_intro_field']);
		
		$this->search_index_fields = (array) Arr::get($values, 'search_index_fields', array());
		unset($values['search_index_fields']);

		$status = parent::save($values);
		
		if(is_array($values))
		{
			$headline_fields = Arr::get($values, 'in_headline', array());
			foreach($this->record()->fields() as $f)
			{
				$value = Arr::get($headline_fields, $f->id, 0);

				$field = DataSource_Hybrid_Field_Factory::get_field($f->id);
				$old_field = clone($field);

				$field->set(array('in_headline' => $value));

				DataSource_Hybrid_Field_Factory::update_field($old_field, $field);
			}
		}
		
		return $status;
	}

	/**
	 * Удаление раздела
	 * 
	 * @return \DataSource_Hybrid_Section
	 */
	public function remove() 
	{
		$this->record()->destroy();
		DataSource_Hybrid_Factory::remove($this->id());
		return parent::remove();
	}
	
	/**
	 * Создание нового документа
	 * 
	 * @param DataSource_Hybrid_Document $doc
	 * @return DataSource_Hybrid_Document
	 */
	public function create_document( $doc ) 
	{
		$doc->id = $this->create_empty_document($doc->header);

		$record = $this->record();
		$record->initialize_document($doc);
		$query = $record->get_sql($doc);

		$success = TRUE;
	
		foreach($query as $q)
		{
			$_query = DB::query(Database::UPDATE, $q)->execute();
		}

		if($success) 
		{
			$this->update_size();
			$this->add_to_index(array($doc->id));
		} 
		else 
		{
			$this->remove_documents(array($doc->id));
			$doc->id = 0;
		}
		
		$this->clear_cache();
		
		return $doc;
	}
	
	/**
	 * Обновление документа
	 * 
	 * @param DataSource_Hybrid_Document $doc
	 * @return boolean
	 */
	public function update_document( $doc ) 
	{
		$old = $this->get_document($doc->id);
	
		if( empty($old) )
		{
			return FALSE;
		}

		$record = $this->record();
		$record->document_changed($old, $doc);
		$query = $record->get_sql($doc, TRUE);

		$result = TRUE;
		foreach($query as $q)
		{
			$result = DB::query(NULL, $q)->execute() AND $result;
		}

		if( $doc->is_changed('published')) 
		{
			if( $doc->published === TRUE )
			{
				$this->add_to_index(array($old->id));
			}
			else
			{
				$this->remove_from_index(array($old->id));
			}
		} 
		elseif( $old->published === TRUE )
		{
			$this->update_index(array($old->id));
		}
		
		$this->clear_cache();

		return $result;
	}
	
	/**
	 * Загрузка документа по ID
	 * 
	 * @param integer $id
	 * @return \DataSource_Hybrid_Document
	 */
	public function get_document($id)
	{
		$document = NULL;

		if( empty($id) ) return NULL;
		
		$document = new DataSource_Hybrid_Document($this->record(), $id);
		return $document->load($id);
	}
	
	/**
	 * Загрузка документов раздела в формате для индексации
	 * 
	 * В этом методе происходит загрукзка индексируемых полей документа 
	 * + поля описания документа
	 * 
	 * @param array $id
	 * @return array array([ID] => array('id', 'header', 'content', 'intro', ....), ...)
	 */
	public function get_indexable_documents( array $id = NULL ) 
	{
		$result = array();
		$fields = $this->search_index_fields;
		
		if(!empty($this->search_intro_field))
		{
			$fields[] = $this->search_intro_field;
		}

		$agent = DataSource_Hybrid_Agent::instance($this->id(), $this->id());
		
		$query = $agent->get_query_props($this->search_index_fields);
		
		if(is_array($id) AND !empty($id))
		{
			$query->where('d.id', 'in', $id);
		}
		else if(!empty($id))
		{
			$query->where('d.id', '=', (int) $id);
		}
		
		foreach ($query->execute() as $row)
		{
			$doc_id = $row['id'];
			$result[$doc_id] = array(
				'id' => $row['id'],
				'intro' => Arr::get($row, $this->search_intro_field),
				'header' => $row['header']
			);
			
			unset(
					$row['id'], 
					$row['ds_id'], 
					$row['published'], 
					$row['created_on'],
					$row['updated_on'], 
					$row['header']
			);
			$content = '';
			foreach ($row as $key => $value)
			{
				$content .= ' ' . (string)$value;
			}
			
			$result[$doc_id]['content'] = $content;
		}
	
		return $result;
	}
	
	/**
	 * Получение пустого объекта документа
	 * 
	 * @return \DataSource_Hybrid_Document
	 */
	public function get_empty_document() 
	{
		return new DataSource_Hybrid_Document($this->record());
	}
	
	/**
	 * Создание пустого документа и возврат его ID
	 * 
	 * @param string $header
	 * @return null|integer Идентификатор документа
	 */
	public function create_empty_document( $header ) 
	{
		$data = array(
			'ds_id' => $this->id(),
			'header' => $header,
			'created_on' => date('Y-m-d H:i:s'),
		);
		
		$query = DB::insert('dshybrid')
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();

		$id = $query[0];

		$success = TRUE;

		if( $id )
		{
			$query = DB::insert("dshybrid_" . $this->id())
				->columns(array('id'))
				->values(array($id))
				->execute();
			
			return $id;
		}
		
		$this->remove_documents( array($id) );

		return NULL;
	}
	
	/**
	 * Удаление документов по ID
	 * 
	 * @see DataSource_Hybrid_Document::remove()
	 * 
	 * @param array $ids
	 * @return \DataSource_Hybrid_Section
	 */
	public function remove_documents( array $ids = NULL  ) 
	{
		if( empty($ids) ) return $this;
		
		foreach ($ids as $id)
		{
			$document = $this->get_empty_document()->load($id);
			if($document->loaded())
			{
				$this->record()->destroy_document($document);
				$document->remove();
			}
		}

		$this->update_size();
		$this->remove_from_index($ids);
		$this->clear_cache();

		return parent::remove_documents($ids);
	}
	
	/**
	 * Получение полного пути до файла шаблона
	 * 
	 * @return string
	 */
	public function template()
	{
		$snippet = new Model_File_Snippet($this->template);
		
		$template = NULL;

		if( $snippet->is_exists() )
		{
			$template = $snippet->get_file();
		}
		else if(($template = $snippet->find_file()) === FALSE)
		{
			$template = NULL;
		}
		
		return $template;
	}
	
	/**
	 * Очистка кеша виджетов раздела
	 */
	public function clear_cache( )
	{
		Datasource_Data_Manager::clear_cache( $this->id(), DataSource_Hybrid_Factory::$widget_types);
	}

	protected function _serialize()
	{
		$vars = parent::_serialize();

		unset(
			$vars['_agent'], 
			$vars['_record'],
			$vars['indexed_doc_query']
		);
		
		return $vars;
	}
	
	protected function _initialize()
	{
		parent::_initialize();
		
		$this->_record = NULL;
		$this->_agent = NULL;
		$this->indexed_doc_query = NULL;
	}
}