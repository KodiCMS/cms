<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Decorator
 * @author		ButscHSter
 */
abstract class Model_Widget_Decorator {
	
	const ORDER_ASC = 'ASC';
	const ORDER_DESC = 'DESC';


	/**
	 * Идентификатор виджета
	 * @var integer
	 */
	public $id;

	/**
	 * Тип виджета
	 * @var string 
	 */
	public $type;
	
	/**
	 * Название виджета
	 * @var string 
	 */
	public $name;

	/**
	 * Описание виджета
	 * @var string 
	 */
	public $description = '';
	
	/**
	 * Заголовок в шаблоне
	 * @var string 
	 */
	public $header = NULL;

	/**
	 * Файл шаблона
	 * @var string
	 */
	public $template = NULL;
	
	/**
	 * Файл шаблона настроек виджета
	 * @var string 
	 */
	public $backend_template = NULL;
	
	/**
	 * Файл шаблона вывода
	 * @var string 
	 */
	public $frontend_template = NULL;


	/**
	 * Виджет использует шаблон
	 * @var boolean 
	 */
	public $use_template = TRUE;

	
	/**
	 * Параметры передаваемые в шаблон
	 * @var array
	 */
	public $template_params = array();

	/**
	 * Название блока, в который помещен виджет
	 * @var string
	 */
	public $block = NULL;
	
	/**
	 * Позиция виджета в блоке
	 * @var integer
	 */
	public $position = 500;
	
	/**
	 * Виджет влияет на хлебные крошки
	 * @var boolean 
	 */
	public $crumbs = FALSE;
	
	/**
	 * Виджет можно кешировать
	 * @var boolean 
	 */
	public $use_caching = TRUE;

	/**
	 * Включение / Отключение кеширования
	 * @var bool 
	 */
	public $caching = FALSE;
	
	/**
	 * Время хранения кеша (По умолчанию месяц)
	 * @var integer 
	 */
	public $cache_lifetime = Date::MONTH;
	
	/**
	 * Теги кеширования
	 * @var array 
	 */
	public $cache_tags = array();
	
	/**
	 * Роли, с которыми видно виджет. 
	 * Если не указаны, то видно всем
	 * 
	 * @var array 
	 */
	public $roles = array();


	/**
	 * 
	 * @var bool 
	 */
	public $throw_404 = FALSE;
	
	/**
	 *
	 * @var Context 
	 */
	protected $_ctx = NULL;


	/**
	 * Дополнительные параметры виджета
	 * @var array 
	 */
	protected $_data = array();
	
	public function __construct()
	{
		$this->_set_type();
	}

	/**
	 * Сеттер доплнительных параметров
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Model_Widget_Decorator
	 */
	public function set( $name, $value )
	{
		$this->_data[$name] = $value;
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Model_Widget_Decorator
	 */
	public function bind( $name, & $value )
	{
		$this->_data[$name] = & $value;
		return $this;
	}

	/**
	 * Геттер дополнительных параметров
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mided
	 */
	public function & get( $name, $default = NULL)
	{
		$result = $default;
		if (array_key_exists($name, $this->_data))
		{
			$result = $this->_data[$name];
		}
		
		return $result;
	}

	/**
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set( $name, $value )
	{
		$this->set($name, $value);
	}
	
	/**
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function & __get( $name )
	{
		return $this->get( $name );
	}

	/**
	 * Получения пути до шаблона настроек виджета.
	 * Если шаблон не указан в параметре {@see Model_Widget_Decorator::backend_template},
	 * то он основывается на типе виджета
	 * 
	 * @return string
	 */
	public function backend_template()
	{
		if($this->backend_template === NULL)
		{
			$this->backend_template = $this->type;
		}
		
		return $this->backend_template;
	}
	
	/**
	 * Получения пути до шаблона для Frontend.
	 * Если шаблон не указан в параметре {@see Model_Widget_Decorator::frontend_template},
	 * то он основывается на типе виджета
	 * 
	 * @return string
	 */
	public function frontend_template()
	{
		if($this->frontend_template === NULL)
		{
			$this->frontend_template = $this->type;
		}
		
		return $this->frontend_template;
	}
	
	/**
	 * Шаблон по умолчанию.
	 * 
	 * @return string
	 */
	public function default_template()
	{
		if( ($template = Kohana::find_file('views', 'widgets/frontend/' . $this->frontend_template())) === FALSE  )
		{
			$template = Kohana::find_file('views', 'widgets/frontend/default');
		}

		return $template;
	}
	
	/**
	 * Получение пути до файла шаблона виджета.
	 * Сначала происходит поиск файла по названию Сниппета, если он не 
	 * найден, то просходит поиск шаблона по умолчанию для виджета.
	 * 
	 * @return string
	 */
	protected function _fetch_template()
	{
		if( empty($this->template) ) 
		{
			$this->template = $this->default_template();
		}
		else
		{
			$snippet = new Model_File_Snippet($this->template);
			
			if( $snippet->is_exists() )
			{
				$this->template = $snippet->get_file();
			}
			else if(($this->template = $snippet->find_file()) === FALSE)
			{
				$this->template = $this->default_template();
			}
		}
		
		return $this->template;
	}

	/**
	 * Подготовка к рендеру виджета
	 * Происходит инициализация View и передача в него переменных из метода
	 * {@see Model_Widget_Decorator::fetch_data()}, а также дополнительных параметров,
	 * переданных в блок шаблона страницы
	 * 
	 * @param array $params
	 * @return View
	 */
	protected function _fetch_render()
	{
		$context = & Context::instance();

		$data = $this->fetch_data();
		$data['params'] = $this->template_params;
		$data['page'] = $context->get_page();
	
		return View_Front::factory($this->template, $data)
			->bind('header', $this->header)
			->bind('ctx', $this->_ctx);
	}

	/**
	 * Установка настроек кеша
	 * 
	 * @param array $data array(['caching'] => [BOOLEAN], 'cache_lifetime' => [INTEGER], 'cache_tags' => [ARRAY])
	 * @return \Model_Widget_Decorator
	 */
	public function set_cache_settings(array $data)
	{
		$this->caching = (bool) Arr::get($data, 'caching', FALSE);
		$this->cache_lifetime = (int) Arr::get($data, 'cache_lifetime');
		
		$this->cache_tags = explode(',', Arr::get($data, 'cache_tags'));
		
		unset($data['caching'], $data['cache_lifetime'], $data['cache_tags']);
		
		return $this;
	}

	/**
	 * Получение идентификатора кеша. Используется для кеширования виджета.
	 * 
	 * Каждый виджет имеет свой, уникальный идентификатор кеша, из которого он 
	 * получает закешированный HTML шаблон, если ваш виджет в зависимости от параметров
	 * URL или любых других параметров должен изменять данные, то их необходимо указать
	 * в строке идентификатора кеща.
	 * 
	 * @return string
	 */
	public function get_cache_id()
	{
		return 'Widget::' . $this->type . '::' . $this->id;
	}
	
	/**
	 * Получение списка тегов кеширования в виде строки
	 * 
	 * @return string
	 */
	public function cache_tags()
	{
		return implode(', ', (array) $this->cache_tags);
	}

	/**
	 * Метод очистки кеша виджета.
	 * 
	 * @todo Не совсем корректно работает с динамическим кешем
	 * @return \Model_Widget_Decorator
	 */
	public function clear_cache()
	{
		if($this->caching === TRUE)
		{
			Fragment::delete($this->get_cache_id());
		}

		return $this;
	}
	
	/**
	 * Метод очистки кеша по тегам
	 * @return \Model_Widget_Decorator
	 */
	public function clear_cache_by_tags()
	{
		if(!empty($this->cache_tags) AND Kohana::$caching === TRUE)
		{
			$cache = Cache::instance();
			
			if($cache instanceof Cache_Tagging)
			{
				if( is_array( $this->cache_tags ))
				{
					foreach($this->cache_tags as $tag)
					{
						$cache->delete_tag($tag);
					}
				}
				else
				{
					$cache->delete_tag( $this->cache_tags );
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Проверка виджета на существование в БД
	 * 
	 * @return bool
	 */
	public function loaded()
	{
		return isset($this->id) AND $this->id > 0;
	}
	
	/**
	 * Метод используется для поиска и подключения шаблона настроек для админ 
	 * интерфейса.
	 * Если шаблон настроек не найдет, то выводятся стандартные настройки для виджета
	 * 
	 * @return View|null
	 */
	public function fetch_backend_content()
	{
		try
		{
			$content = View::factory( 'widgets/backend/' . $this->backend_template(), array(
					'widget' => $this
				))->set($this->backend_data());
		}
		catch( Kohana_Exception $e)
		{
			$content = NULL;
		}
		
		return $content;
	}
	
	/**
	 * Параметры, которые передаются в шаблон настроек виджета
	 * 
	 * @see Model_Widget_Decorator::fetch_backend_content()
	 * @return array
	 */
	public function backend_data()
	{
		return array();
	}
	
	/**
	 * Метод используется для сохранения настроек виджета. 
	 * Вызывается в момент сохранения виджета.
	 * 
	 * @param array $data
	 */
	public function set_values(array $data)
	{
		if(empty($data['roles']))
		{
			$data['roles'] = array();
		}

		foreach($data as $key => $value)
		{
			if( method_exists( $this, 'set_' . $key ))
			{
				$this->{$key} = $this->{'set_'.$key}($value);
			}
			else 
			{
				$this->{$key} = $value;
			}
		}
		
		return $this;
	}

	/**
	 * Рендер виджета
	 * @param array $params Дополнительные параметры
	 */
	public function run(array $params = array()) 
	{
		return $this->render($params);
	}
	
	/**
	 * Передача дополнительных парамтеров в виджет
	 * @param array $params Дополнительные параметры
	 */
	public function set_params(array $params = array()) 
	{
		$this->template_params = Arr::merge($params, $this->template_params);
		
		return $this;
	}

	/**
	 * Рендер виджета во Frontend
	 * 
	 * Отключение комментариев для блока
	 * 
	 *		Block::run('block_name', array('comments' => FALSE));
	 * 
	 * Отключение кеширования виджетов в блоке
	 * 
	 *		Block::run('block_name', array('caching' => FALSE));
	 * 
	 * @param array $params Дополнительные параметры
	 */
	public function render(array $params = array())
	{
		// Проверка правк на видимость виджета
		if( ! empty($this->roles))
		{
			$auth = Auth::instance();
			if( $auth->logged_in() )
			{
				if( ! $auth->get_user()->has_role($this->roles, FALSE) )
				{
					return;
				}
			}
			else
			{
				return;
			}
		}
		
		if(Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('Widget render', $this->name);
		}

		$this->_fetch_template();
		$this->set_params($params);
		
		$allow_omments = (bool) Arr::get($this->template_params, 'comments', TRUE);
		$caching = (bool) Arr::get($this->template_params, 'caching', $this->caching);

		if( $this->block == 'PRE' OR $this->block == 'POST' )
		{
			$allow_omments = FALSE;
		}
		
		if($allow_omments)
		{
			echo "<!--{Widget: {$this->name}}-->";
		}
		
		if(Kohana::$caching === FALSE OR $caching === FALSE)
		{
			$this->caching = FALSE;
		}
		
		if(
			$this->caching === TRUE
		AND 
			! Fragment::load($this->get_cache_id(), $this->cache_lifetime, TRUE)
		)
		{
			echo $this->_fetch_render();
			Fragment::save_with_tags($this->cache_lifetime, $this->cache_tags);
		}
		else if( ! $this->caching )
		{
			echo $this->_fetch_render();
		}

		if($allow_omments)
		{
			echo "<!--{/Widget: {$this->name}}-->";
		}
		
		if(isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
	}
	
	/**
	 * Установка типа виджета
	 * 
	 * @return \Model_Widget_Decorator
	 */
	protected function _set_type()
	{
		$class_name = get_called_class();
		$this->type = strtolower(substr($class_name, 13));
		
		return $this;
	}

	/**
	 * Функция запоскается через обсервер frontpage_found
	 * 
	 * @see Context::init_widgets()
	 */
	public function on_page_load() {}
	
	/**
	 * Функция запоскается через обсервер frontpage_render
	 * 
	 * @see Context::init_widgets()
	 */
	public function after_page_load() {}
	
	/**
	 * Метод изменения хлебных крошек страницы. Вызывается в момент передачи
	 * виджетов на страницу сайта.
	 *  
	 * @see Context::build_crumbs()
	 * @param Breadcrumbs $crumbs
	 */
	public function change_crumbs( Breadcrumbs &$crumbs) {}
	
	/**
	 * Метод используется для передачи списска параметров в шаблон вывода
	 * виджета во Frontend
	 * 
	 *		public function fetch_data()
	 *		{
	 *			....
	 * 
	 *			return array(
	 *				'data' => 'test',
	 *				'param' => 'value'
	 *			);
	 *		}
	 * 
	 * 
	 *		//Frontend snippet template
	 *		echo $data;  // return test
	 *		echo $param;  // return value
	 * 
	 * @return array array([KEY] => [VALUE], ....)
	 */
	abstract public function fetch_data();

	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->render();
	}
	
	public function __sleep()
	{
		$vars = get_object_vars($this);

		unset(
			$vars['_ctx'],
			$vars['id'],
			$vars['type'],
			$vars['template'],
			$vars['name'], 
			$vars['description'],
			$vars['backend_template'],
			$vars['frontend_template'],
			$vars['use_template'],
			$vars['block'],
			$vars['position'],
			$vars['template_params']
		);

		return array_keys($vars);
	}
	
	public function __wakeup()
	{
		$this->_ctx = Context::instance();
		$this->_set_type();
	}
}