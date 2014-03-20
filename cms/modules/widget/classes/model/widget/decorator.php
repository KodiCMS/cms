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
	 *
	 * @var integer
	 */
	public $id;

	/**
	 *
	 * @var string 
	 */
	public $type;
	
	/**
	 *
	 * @var string 
	 */
	public $name;

	/**
	 *
	 * @var string 
	 */
	public $description = '';
	
	/**
	 *
	 * @var string 
	 */
	public $header = NULL;

	/**
	 *
	 * @var string
	 */
	public $template = NULL;
	
	/**
	 *
	 * @var string 
	 */
	public $backend_template = NULL;
	
	/**
	 *
	 * @var string 
	 */
	public $frontend_template = NULL;


	/**
	 *
	 * @var boolean 
	 */
	public $use_template = TRUE;

	
	/**
	 *
	 * @var array
	 */
	public $template_params = array();

	/**
	 *
	 * @var string
	 */
	public $block = NULL;
	
	/**
	 *
	 * @var integer
	 */
	public $position = 500;
	
	/**
	 *
	 * @var boolean 
	 */
	public $crumbs = FALSE;
	
	/**
	 *
	 * @var boolean 
	 */
	public $use_caching = TRUE;

	/**
	 *
	 * @var bool 
	 */
	public $caching = FALSE;
	
	/**
	 *
	 * @var integer 
	 */
	public $cache_lifetime = Date::MONTH;
	
	/**
	 *
	 * @var array 
	 */
	public $cache_tags = array();
	
	/**
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
	 *
	 * @var array 
	 */
	protected $_data = array();
	
	public function __construct()
	{
		$this->_set_type();
	}

	/**
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
	 * 
	 * @param array $params
	 * @return View
	 */
	protected function _fetch_render($params)
	{
		$params = Arr::merge($params, $this->template_params);
		$context = & Context::instance();

		$data = $this->fetch_data();
		$data['params'] = $params;
		$data['page'] = $context->get_page();
	
		return View_Front::factory($this->template, $data)
			->bind('header', $this->header)
			->bind('ctx', $this->get( 'ctx' ));
	}

	/**
	 * 
	 * @param array $data
	 * @return \Model_Widget_Decorator
	 */
	public function set_cache_settings(array $data)
	{
		$this->caching = (bool) Arr::get($data, 'caching', FALSE);
		$this->cache_lifetime = (int) Arr::get($data, 'cache_lifetime');
		
		$this->cache_tags = explode(',', Arr::get($data, 'cache_tags'));
		
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function get_cache_id()
	{
		return 'Widget::' . $this->type . '::' . $this->id;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function cache_tags()
	{
		return implode(', ', (array) $this->cache_tags);
	}

	/**
	 * 
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
	 * 
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
	 * 
	 * @return bool
	 */
	public function loaded()
	{
		return isset($this->id) AND $this->id > 0;
	}
	
	/**
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
	 * 
	 * @return array
	 * @deprecated
	 */
	public function load_template_data()
	{
		return $this->backend_data();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function backend_data()
	{
		return array();
	}
	
	/**
	 * @param array $data
	 */
	public function set_values(array $data)
	{
		unset($data['caching'], $data['cache_lifetime'], $data['cache_tags']);

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
	 * 
	 * @param array $params
	 */
	public function run($params = array()) 
	{
		return $this->render($params);
	}

	/**
	 * 
	 * @param array $params
	 */
	public function render($params = array())
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
		
		$allow_omments = (bool) Arr::get($params, 'comments', TRUE);

		if( $this->block == 'PRE' OR $this->block == 'POST' )
		{
			$allow_omments = FALSE;
		}
		
		if($allow_omments)
		{
			echo "<!--{Widget: {$this->name}}-->";
		}
		
		if(Kohana::$caching === FALSE)
		{
			$this->caching = FALSE;
		}
		
		if(
			$this->caching === TRUE
		AND 
			! Fragment::load($this->get_cache_id(), $this->cache_lifetime, TRUE)
		)
		{
			echo $this->_fetch_render($params);
			Fragment::save_with_tags($this->cache_lifetime, $this->cache_tags);
		}
		else if( ! $this->caching )
		{
			echo $this->_fetch_render($params);
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
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->render();
	}
	
	public function __wakeup()
	{
		$this->_ctx = Context::instance();
		
		$this->_set_type();
	}
	
	protected function _set_type()
	{
		$class_name = get_called_class();
		$this->type = strtolower(substr($class_name, 13));
		
		return $this;
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

	/**
	 * Функция запоскается через обсервер frontpage_found
	 */
	public function on_page_load() {}
	
	/**
	 * Функция запоскается через обсервер frontpage_render
	 */
	public function after_page_load() {}
	
	/**
	 * 
	 * @param type $crumbs
	 */
	public function change_crumbs( Breadcrumbs &$crumbs) {}
	
	/**
	 * @return array
	 */
	abstract public function fetch_data();
}