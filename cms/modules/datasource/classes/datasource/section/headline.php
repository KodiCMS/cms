<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		Datasource
 */
abstract class Datasource_Section_Headline {
	
	/**
	 * Объект раздела
	 * 
	 * @var Datasource_Section 
	 */
	protected $_section = NULL;

	/**
	 * Правила сортировки списка документов
	 * @var array 
	 */
	protected $_sorting = array(
		array('created_on' => 'desc')
	);
	
	/**
	 * Объект постраничной навигации
	 * @var Pagination 
	 */
	protected $_pagination = NULL;


	/**
	 * Кол-во документов выводимых на 1 странице
	 * По умолчанию 20
	 * 
	 * Используется объектом постраничной навигации
	 * 
	 * @var integer 
	 */
	protected $_limit = 20;
	
	/**
	 * Кол-во пропускаемых документов
	 * 
	 *  Используется объектом постраничной навигации
	 * 
	 * @var integer 
	 */
	protected $_offset = 0;

	/**
	 * 
	 * @param Datasource_Section $section
	 */
	public function __construct(Datasource_Section $section)
	{
		$this->_section = $section;
		$this->_pagination = Pagination::factory();
	}

	/**
	 * Поля раздела, которые отображаются в списке
	 * 
	 * @return array Fields array([Field name] => array('name' => [Field header]))
	 */
	public function fields()
	{
		return array(
			'id' => array(
				'name' => 'ID',
				'width' => 50
			),
			'header' => array(
				'name' => 'Header',
				'width' => NULL,
				'type' => 'link'
			)
		);
	}
	
	/**
	 * Рендер View спсика документов раздела
	 * 
	 * @param type $template Путь для своего шаблона
	 * @return View
	 */
	public function render($template = NULL)
	{
		if($template === NULL)
		{
			$template = 'datasource/' . $this->_section->type() . '/headline';
		}

		return View::factory($template, array(
			'fields' => $this->fields(),
			'data' => $this->get(),
			'pagination' => $this->pagination()
		));
	}
	
	/**
	 * Рендер View спсика документов раздела
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->render();
	}
	
	/**
	 * Геттер и Сеттер лимита
	 * 
	 * @param integer $limit
	 * @return integer
	 */
	public function limit( $limit = NULL )
	{
		if($limit !== NULL)
		{
			$this->_limit = (int) $limit;
			
			if($this->_limit < 1)
			{
				$this->_limit = 1;
			}
		}

		return (int) $this->_limit;
	}
	
	/**
	 * Возвращает кол-во пропускаемых документов
	 * 
	 * @param integer $limit
	 * @return integer
	 */
	public function offset()
	{
		return (int) $this->_offset;
	}
	
	/**
	 * Формирование данных для постраничной навигации
	 * 
	 * @param array $ids
	 * @param string $search_word
	 * @return Pagination
	 */
	public function pagination( array $ids = NULL )
	{
		$this->_pagination->setup(array(
			'items_per_page' => $this->limit(),
			'total_items' => $this->count_total($ids),
			'current_page' => array(
				'source' => 'query_string',
				'key' => 'page'
			)
		));

		$this->_offset = (int) $this->_pagination->offset;
		
		return $this->_pagination;
	}

	/**
	 * Получение списка документов в виде массива
	 * 
	 * 
	 * @param array $ids
	 * @return array array( 'total' => ..., 'documents' => array([id] => array([Field name] => $value, ....)))
	 */
	abstract public function get( array $ids = NULL );
	
	/**
	 * Подсчет кол-ва документов в разделе
	 * 
	 * @param array $ids
	 * @param string $search_word
	 * @return integer
	 */
	abstract public function count_total( array $ids = NULL );
	
	/**
	 * Метод используется для поиска по документам по ключевому слову.
	 * 
	 * Ключевое слово передается в качестве $_GET запроса с ключем "keyword"
	 * 
	 * @param Database_Query $query
	 * @return Database_Query
	 */
	public function search_by_keyword( Database_Query $query )
	{
		$keyword = Request::initial()->query('keyword');
		
		if(empty($keyword))
		{
			return $query;
		}

		if($this->_section->is_indexable())
		{
			$query = Search::instance()->get_module_query($query, $keyword, 'ds_' . $this->_section->id());
		}
		else
		{
			$query
				->where_open()
				->where('d.id', 'like', '%'.$keyword.'%')
				->or_where('d.header', 'like', '%'.$keyword.'%')
				->where_close();
		}
		
		return $query;
	}
}