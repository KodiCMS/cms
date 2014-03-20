<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Datasource
 */
abstract class Datasource_Section {
	
	/**
	 * Фабрика создания раздела данных
	 * 
	 * @param string $type Тип раздела
	 * 
	 * @return \Datasource_Section
	 */
	public static function factory($type)
	{
		$class = 'Datasource_Section_' . ucfirst($type);
		
		if( ! class_exists($class))
		{
			throw new DataSource_Exception('Class :class_name not exists', 
					array(':class_name' => $class));
		}
		
		return new $class($type);
	}

	/**
	 * Загрузка разедла по ID
	 * 
	 * @param integer $id
	 * @return null|Datasource_Section
	 */
	public static function load( $id ) 
	{
		if($id === NULL)
		{
			return NULL;
		}
		
		$query = DB::select('docs', 'indexed', 'code')
			->from('datasources')
			->where('id', '=', (int) $id)
			->execute()
			->current();
	
		if( empty($query) )
		{
			return NULL;
		}

		$result = unserialize($query['code']);

		$result->_id = $id;
		$result->_docs = (int) $query['docs'];
		$result->_is_indexable = (bool) $query['indexed'];

		return $result;
	}

	/**
	 * Идентификатор раздела
	 * 
	 * @var integer
	 */
	protected $_id;
	
	/**
	 * Тип раздела
	 * 
	 * @var string
	 */
	protected $_type;
	
	/**
	 * Название раздела
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Описание раздела
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Кол-во документов в разделе
	 * 
	 * @var integer
	 */
	protected $_docs = 0;
	
	/**
	 * Таблица раздела в БД
	 * 
	 * @var string
	 */
	protected $_ds_table;
	
	/**
	 * Индексировать раздел
	 * 
	 * @var boolean
	 */
	protected $_is_indexable = FALSE;
	
	/**
	 * Объект загрузки списка документов 
	 * 
	 * @var type Datasource_Section_Headline
	 */
	protected $_headline = NULL;


	/**
	 * 
	 * @param string $type
	 */
	public function __construct( $type ) 
	{
		$this->_type = $type;
		
		$this->_initialize();
	}
	
	/**
	 * Возвращает тип раздела
	 * 
	 * @return string
	 */
	public function type()
	{
		return $this->_type;
	}
	
	/**
	 * Возвращает идентификатор раздела
	 * 
	 * @return integer
	 */
	public function id()
	{
		return $this->_id;
	}
	
	/**
	 * Проверка раздела на существование
	 *  
	 * @return boolean
	 */
	public function loaded()
	{
		return $this->_id !== NULL;
	}
	
	/**
	 * Возвращает отбъект списка документов
	 * 
	 * @return Datasource_Section_Headline
	 */
	public function headline()
	{
		return $this->_headline;
	}

	/**
	 * Создание раздела 
	 * 
	 * @param array $values Массив полей раздела
	 *
	 * @return integer Идентификатор раздела
	 * @throws DataSource_Exception
	 */
	public function create( array $values ) 
	{
		$this->validate($values);

		$this->name = Arr::get($values, 'name');
		$this->description = Arr::get($values, 'description');
		$this->_is_indexable = (bool) Arr::get($values, 'is_indexable');
		
		$data = array(
			'type' => $this->_type,
			'indexed' => (bool) $this->_is_indexable,
			'description' => $this->description,
			'name' => $this->name,
			'created_on' => date('Y-m-d H:i:s'),
			'code' => serialize($this)
		);
		
		$query = DB::insert('datasources')
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();

		$this->_id = $query[0];
		
		if(empty($this->_id))
		{
			throw new DataSource_Exception('Datasource section :name not created', 
					array(':name' => $this->name));
		}
		
		unset($query, $values);
		
		return $this->_id;
	}
	
	/**
	 * Обновление раздела.
	 * 
	 * При сохранении раздела в БД происходит его сериализация и сохарение данных
	 * в поле "code". Список полей, которые не должын попадать в БД указывается в 
	 * методе {@see _serialize()}
	 * 
	 * @param array $values
	 * @return boolean
	 */
	public function save( array $values = NULL) 
	{
		if( ! $this->loaded())
		{
			return FALSE;
		}
		
		if(is_array($values))
		{
			$this->validate($values);

			$this->name = Arr::get($values, 'name');
			$this->description = Arr::get($values, 'description');
		
			$this->set_indexable(Arr::get($values, 'is_indexable', FALSE));
		}
		
		$data = array(
			'indexed' => $this->_is_indexable,
			'name' => $this->name,
			'description' => $this->description,
			'updated_on' => date('Y-m-d H:i:s'),
			'code' => serialize( $this )
		);
		
		DB::update('datasources')
			->set($data)
			->where( 'id', '=', $this->_id )
			->execute();

		$this->update_size();
		
		unset($data, $values);
		
		return TRUE;
	}
	
	/**
	 * Удаление раздела
	 * 
	 * При удалении раздела происходит удаление документов.
	 * 
	 * @return \Datasource_Section
	 */
	public function remove()
	{
		$this->remove_documents();
		
		DB::delete('datasources')
			->where('id', '=', $this->_id)
			->execute();

		$this->_id = NULL;
		
		return $this;
	}
	
	/**
	 * Удаление разделов документа
	 * 
	 * @see Datasource_Section::remove()
	 * 
	 * @return \Datasource_Section
	 */
	public function remove_documents( array $ids = NULL )
	{
		$query = DB::delete($this->_ds_table);
		
		if( ! empty($ids) )
		{
			$query->where('id', 'in', $ids);
		}
		
		$query->execute();
		
		unset($query);
		
		return $this;
	}
	
	/**
	 * Загрузка документа раздела по ID
	 * 
	 * @param integer $id
	 * @return DataSource_Document
	 */
	abstract public function get_document( $id );
	
	/**
	 * Получение пустого объекта документа
	 * 
	 * @return \DataSource_Document
	 */
	abstract public function get_empty_document();
	
	/**
	 * Публикация документов по ID
	 * 
	 * @param array $ids
	 * @return \Datasource_Section
	 */
	public function publish(array $ids) 
	{
		return $this->_publish($ids, TRUE);
	}

	/**
	 * Снятие документов с публикации по ID
	 * 
	 * @param array $ids
	 * @return \Datasource_Section
	 */
	public function unpublish(array $ids) 
	{
		return $this->_publish($ids, FALSE);
	}
	
	/**
	 * Смена статуса документов по ID.
	 * 
	 * @param array $ids
	 * @param boolean $value
	 * @return \Datasource_Section
	 */
	protected function _publish(array $ids, $status) 
	{
		DB::update($this->_ds_table)
			->set(array(
				'published' => (bool) $value,
				'updated_on' => date('Y-m-d H:i:s'),
			))
			->where('id', 'in', $ids)
			->where('ds_id', '=', $this->_id)
			->execute();

		if($value === TRUE)
		{
			$this->add_to_index($ids);
		}
		else
		{
			$this->remove_from_index($ids);
		}

		return $this;
	}
	
	/**
	 * Обновление поля кол-ва документов в разделе
	 * 
	 * @return \Datasource_Section
	 */
	public function update_size() 
	{
		if($this->_ds_table) 
		{
			DB::update('datasources')
				->set(array(
					'docs' => DB::select(DB::expr('COUNT("*")'))
						->from($this->_ds_table)
						->where('ds_id', '=', $this->_id)
				))
				->where('id', '=', $this->_id)
				->execute();
		}
		
		return $this;
	}
	
	/**
	 * Валидация данных полей раздела
	 * 
	 * @param array $array
	 * @throws Validation_Exception
	 */
	public function validate( array $array )
	{
		$validation = Validation::factory($array)
			->rules('name', array(
				array('not_empty')
			))
			->label('name', __('Header') );
		
		if( ! $validation->check() )
		{
			throw new Validation_Exception($array);
		}
	}
	
	/**
	 * Вызывается при сохранении раздела в БД
	 * 
	 * @return array
	 */
	public function __sleep()
	{
		return array_keys($this->_serialize());
	}
	
	/**
	 * Список параметров объекта, которые должны сохраняться в БД.
	 * 
	 * @return array
	 */
	protected function _serialize()
	{
		$vars = get_object_vars($this);
		unset($vars['_docs'], $vars['_is_indexable'], $vars['_headline']);
		
		return $vars;
	}

	/**
	 * При заггрузке данных раздела из БД происходит десериализация объекта из поля
	 * "Code", что по сути является загрузкой раздела и в этот момент вызывается этот метод.
	 * 
	 * Если после загрузки раздела необходимо восстановить связи с другими объектами, их 
	 * необходимо описывать в методе {@see _initialize()}
	 */
	public function __wakeup()
	{
		$this->_initialize();
	}
	
	/**
	 * Инициализация данных раздела при создании или загрузке
	 * @throws Kohana_Exception
	 */
	protected function _initialize()
	{
		$this->_docs = 0;
		$this->_is_indexable = FALSE;

		$headline_class = 'Datasource_Section_' . ucfirst($this->type()) . '_Headline';
		if(!class_exists($headline_class))
		{
			throw new Kohana_Exception('Headline class :class not found', array(
				':class' => $headline_class
			));
		}

		$this->_headline = new $headline_class($this);
	}
	
	
	/**************************************************************************
	 * Search indexation
	 **************************************************************************/

	/**
	 * Состояние поисковой индексации раздела
	 * 
	 * @return boolean
	 */
	public function is_indexable()
	{
		return (bool) $this->_is_indexable;
	}

	/**
	 * Смена статуса поисковой индексации раздела
	 * 
	 * @param boolean $newState
	 * @return \Datasource_Section
	 */
	public function set_indexable( $state ) 
	{
		$state = (bool) $state;

		if( ! $this->loaded() )
		{
			$this->_is_indexable = $state;
			
			return $this;
		}

		if($state == $this->is_indexable())
		{
			return $this;
		}

		if($state) 
		{
			$this->_is_indexable = $state;
			$this->add_to_index();
		} 
		else 
		{
			$this->remove_from_index();
			$this->_is_indexable = $state;
		}
		
		return $this;
	}
	
	/**
	 * Загрузка списка документов по ID в формате для индексации
	 * 
	 * @param integer|array $id
	 * @return array array([ID] => array('id', 'header', 'content', 'intro'), ...)
	 */
	public function get_indexable_documents( array $id = NULL ) 
	{
		$result = DB::select('id', 'header', 'content', 'intro')
			->from($this->_ds_table)
			->where('published', '=', 1)
			->where('ds_id', '=', $this->_id);
		
		if( ! empty($id) )
		{
			$result->where('id', 'in', $id);
		}

		return $result
			->execute()
			->as_array('id');
	}
	
	/**
	 * Добавление документов раздела в поисковый индекс
	 * 
	 * При передаче массива ID другие параметры указывать не нужно, т.к. они 
	 * загрузятся автоматически 
	 * 
	 * @see Datasource_Section::get_indexable_documents()
	 * 
	 * @param array $ids Индентификаторы документов
	 * @param string $header Заголовок документа
	 * @param string $content Индексируемый текст
	 * @param string $intro Описание документа
	 * @return \Datasource_Section
	 */
	public function add_to_index(array $ids = array(), $header = NULL, $content = NULL, $intro = NULL) 
	{
		if( ! $this->is_indexable())
		{
			return $this;
		}

		if(count($ids) == 1 AND $header !== NULL)
		{
			Search::instance()->add_to_index('ds_' . $this->id(), $ids[0], $header, $content, $intro);
		}
		else
		{
			$docs = $this->get_indexable_documents($ids);
			
			foreach($docs as $doc)
			{
				Search::instance()->add_to_index('ds_' . $this->id(), $doc['id'], $doc['header'], $doc['content'], $doc['intro']);
			}
		}
	}
	
	/**
	 * Обновление поискового индекса документов раздела
	 * 
	 * При передаче массива ID другие параметры указывать не нужно, т.к. они 
	 * загрузятся автоматически 
	 * 
	 * @see Datasource_Section::get_indexable_documents()
	 * 
	 * @param array $ids
	 * @param string $header
	 * @param string $content
	 * @param string $intro
	 * @return \Datasource_Section
	 */
	public function update_index(array $ids = array(), $header = NULL, $content = NULL, $intro = NULL) 
	{
		if( ! $this->is_indexable())
		{
			return $this;
		}

		return $this->add_to_index($ids, $header, $content, $intro);
	}
	
	/**
	 * Удаление документов из поискового индекса
	 * 
	 * @param array $ids
	 * @return \Datasource_Section
	 */
	public function remove_from_index( array $ids = NULL) 
	{
		if( ! $this->is_indexable())
		{
			return $this;
		}
		
		Search::instance()->remove_from_index('ds_' . $this->id(), $ids);
	}
}