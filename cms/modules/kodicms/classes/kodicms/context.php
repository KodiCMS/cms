<?php defined('SYSPATH') or die('No direct access allowed.');

class KodiCMS_Context {
	
	/**
	 *
	 * @var Context 
	 */
	protected static $_instance = NULL;

	/**
	 * 
	 * @param array $params
	 * @return Context
	 */
	public static function & instance(array $params = array())
	{
		if(self::$_instance === NULL)
		{
			self::$_instance = new Context($params);
			return self::$_instance;
		}

		return self::$_instance;
	}
	
	/**
	 *
	 * @var Model_Page_Front 
	 */
	protected $_page = NULL;
	
	/**
	 *
	 * @var Request 
	 */
	protected $_request = NULL;
	
	/**
	 *
	 * @var Response 
	 */
	protected $_response = NULL;


	/**
	 *
	 * @var Breadcrumbs 
	 */
	protected $_crumbs = NULL;

	/**
	 *
	 * @var array 
	 */
	protected $_widgets = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_widget_ids = NULL;

	/**
	 *
	 * @var array 
	 */
	protected $_blocks = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_params = array();
	
	/**
	 *
	 * @var Behavior_Route 
	 */
	protected $_behavior_router = array();
	
	/**
	 *
	 * @var Meta 
	 */
	protected $_meta = NULL;

	/**
	 *
	 * @var array 
	 */
	protected $_injections = array();

	/**
	 * 
	 * @param array $params
	 */
	public function __construct(array $params = array())
	{
		$this->_params = $params;
	}
	
	/**
	 * 
	 * @param Meta $meta
	 * @return Meta
	 */
	public function meta(Meta $meta = NULL)
	{
		if($meta !== NULL)
		{
			$this->_meta = $meta;
		}
		
		return $this->_meta;
	}

	/**
	 * 
	 * @param string $param
	 * @return mixed
	 */
	public function &get($param) 
	{
		$result = NULL;
		
		if(isset($this->_params[$param]))
		{
			$result = $this->_params[$param];
		}
		elseif ( $this->request()->query($param) !== NULL) 
		{
			$result = $this->request()->query($param);
		}
		else if($this->request()->post( $param ) !== NULL)
		{
			$result = $this->request()->post( $param );
		}
		elseif(
			$this->behavior_router() instanceof Behavior_Route 
		AND 
			$this->behavior_router()->param($param) !== NULL)
		{
			$result = $this->behavior_router()->param($param);
		}
		else if($this->request()->param( $param ) !== NULL)
		{
			$result = $this->request()->param( $param );
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param string $param
	 * @param mixed $value
	 * @return \Context
	 */
	public function set($param, $value)
	{
		$this->_params[$param] = & $value;

		if( ! empty($this->_injections[$param]) 
				AND is_array($this->_injections[$param])) 
		{
			foreach($this->_injections[$param] as $id => $fields)
			{
				if(isset($this->_widgets[$id]))
				{
					foreach($fields as $field => $param_name)
					{
						$this->inject($this->_widgets[$id], 
								$field, $this->get($param_name));
					}
				}
			}

			unset($this->_injections[$param]);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param Response $response
	 * @return \Context|Response
	 */
	public function response( Response $response = NULL )
	{
		if( $response === NULL )
		{
			return $this->_response;
		}
		
		$this->_response = $response;
		return $this;
	}
	
	/**
	 * 
	 * @param Request $request
	 * @return \Context|Request
	 */
	public function request( Request $request = NULL )
	{
		if( $request === NULL )
		{
			return $this->_request;
		}
		
		$this->_request = $request;
		return $this;
	}

	/**
	 * 
	 * @param Model_Page_Front $page
	 * @return \Context
	 */
	public function set_page( Model_Page_Front & $page )
	{
		$this->_page = & $page;
		$this->_crumbs = $page->breadcrumbs();

		return $this;
	}
	
	/**
	 * 
	 * @return Model_Page_Front
	 */
	public function get_page()
	{
		return $this->_page;
	}
	
	/**
	 * 
	 * @param Behavior_Route $router
	 * @return Behavior_Route
	 */
	public function behavior_router( Behavior_Route $router = NULL )
	{
		if( $router !== NULL )
		{
			$this->_behavior_router = $router;
		}
		
		return $this->_behavior_router;
	}

	/**
	 * 
	 * @return \Context
	 */
	public function throw_404( $message = NULL)
	{
		$this->set('throw_message', $message);

		$this->response()->status(404);
		return $this;
	}

		/**
	 * 
	 * @return Breadcrumbs
	 */
	public function & get_crumbs()
	{
		return $this->_crumbs;
	}

	/**
	 * 
	 * @param integer $id
	 * @return Model_Widget_Decorator
	 */
	public function & get_widget($id)
	{
		$result = NULL;
		
		if(isset($this->_widgets[$id]))
		{
			$result = & $this->_widgets[$id];
		}

		return $result;
	}
	
	/**
	 * 
	 * @param string $block
	 * @return Model_Widget_Decorator
	 */
	public function & get_widget_by_block( $block )
	{
		$result = NULL;
		if( !empty($block) AND isset($this->_blocks[$block]))
		{
			if(count($this->_blocks[$block]) == 1)
			{
				$result = & $this->_blocks[$block][0];
			}
			else
			{
				$result = & $this->_blocks[$block];
			}
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_blocks()
	{
		return array_keys($this->_blocks);
	}

	/**
	 * 
	 * @param array $widgets
	 * @return \Context
	 */
	public function register_widgets( array $widgets )
	{
		foreach ($widgets as $id => $widget)
		{
			$this->_widgets[$id] = $widget;
		}
		
		return $this;
	}
	
	
	public function init_widgets()
	{
		$this->_sort_widgets();
		
		foreach( $this->_widget_ids as $id ) 
		{
			$widget =& $this->_widgets[$id];
			if( !empty($widget->block) ) 
			{
				$this->_blocks[$widget->block][] = & $widget;
				
				if($widget instanceof Model_Widget_Decorator)
				{
					Observer::observe('on_page_load', array(& $widget, 'on_page_load'));
					Observer::observe('after_page_load', array(& $widget, 'after_page_load'));
				}
			}
		}
	}

	/**
	 * 
	 * @param Model_Widget_Decorator | View $widget
	 * @param string $field
	 * @param mixed $value
	 * @return \Context
	 */
	public function inject( & $widget, $field, $value) 
	{
		if($widget instanceof Model_Widget_Decorator 
				OR $widget instanceof View)
		{
			if( ! empty($field) )
			{
				if(method_exists($widget, "set_{$field}"))
				{
					call_user_func(array( & $widget, "set_{$field}"), $value);
				}
				else
				{
					$widget->{$field} = $value;
				}				
			}
		}
		
		return $this;
	}

	/**
	 * 
	 * @param Model_Widget_Decorator|View $widget
	 * @param string $field
	 * @param mixed $param_key
	 * @return \Context
	 */
	public function inject_param( & $widget, $field, $param_key) 
	{
		if($widget instanceof Model_Widget_Decorator 
				OR $widget instanceof View)
		{
			if( strpos( $param_key, '/' ) !== FALSE)
			{
				list($name, $index) = explode('/', $param_key, 2);
			}

			$value = $this->get($param_key);
			if($value !== NULL)
			{
				$this->inject($widget, $field, $value);
			}
			else if(isset($name))
			{
				$this->_injections[$name][$widget->id][$field] = $param_key;
			}
		}
		
		return $this;
	}
	
	protected function _sort_widgets() 
	{
		if( $this->_widget_ids !== NULL ) return;

		$ids = array_keys( $this->_widgets );

		$widgets = array(); 
		$types = array('PRE' => array(), '*named' => array(), 'POST' => array());
	
		foreach($ids as $id)
		{
			if(isset($types[$this->_widgets[$id]->block]))
			{
				$types[$this->_widgets[$id]->block][] = $id;
			}
			else
				$types['*named'][] = $id;
		}
	
		foreach($types as $type => $ids)
		{
			foreach($ids as $id )
			{
				$widgets[$id] = & $this->_widgets[$id];
			}
		}

		$this->_widget_ids = array_keys( $widgets );
		$this->_widgets = & $widgets;
	}

	/**
	 * Каждый виджет может добавлять или изменять хлебные крошки на странице
	 * Этот метод обходит все виджеты и запускает метод change_crumbs()
	 */
	public function build_crumbs()
	{
		foreach($this->_widget_ids as $id)
		{
			if($this->_widgets[$id] instanceof Model_Widget_Decorator 
					AND $this->_widgets[$id]->crumbs)
			{
				$this->_widgets[$id]->change_crumbs($this->_crumbs);
			}
		}
		
		return $this;
	}
	
	/**
	 * Используется для вывода ошибки 404
	 * Если не сбрасывать объект, то все блоки на странице дублируются
	 */
	public function __destruct()
	{
		$this->_widgets = array();
		$this->_widget_ids = array();
		$this->_blocks = array();
		$this->_params = array();
		$this->_injections = array();
	}
}