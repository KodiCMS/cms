<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Widgets
 * @author		ButscHSter
 */
class Widget_Manager {

	/**
	 * Получение всех типов виджетов из конфига
	 * @return array
	 */
	public static function map()
	{
		return Kohana::$config->load( 'widgets' )->as_array();
	}
	
	/**
	 * Фабрика для создания виджета.
	 * 
	 * @param string $type Тип виджеа
	 * @return Model_Widget_Decorator
	 */
	public static function factory( $type )
	{
		$class = 'Model_Widget_' . $type;

		if( ! class_exists($class) )
		{
			throw new Kohana_Exception('Widget :type not exists', 
					array(':type' => $type));
		}
	
		$widget = new $class;

		return $widget;
	}
	
	/**
	 * Получения списка виджетов по их типу
	 * 
	 * @param array $types Тип виджета
	 * @return array array([ID] => Model_Widget_Decorator, ....)
	 */
	public static function get_widgets( array $types = NULL )
	{
		$result = array( );

		$res = DB::select( 'w.*' )
				->select( array( DB::expr( 'COUNT(:table)' )->param(
							':table', Database::instance()->quote_column( 'pw.page_id' ) ), 'used' ) )
				->from( array( 'widgets', 'w' ) )
				->join( array( 'page_widgets', 'pw' ), 'left' )
					->on( 'w.id', '=', 'pw.widget_id' )
				->group_by( 'w.id' )
				->order_by( 'w.name' );
		
		if( ! empty($types) )
		{
			$res->where( 'w.type', 'in', $types );
		}
		
		$res = $res->execute()->as_array('id');

		foreach($res as $id => $widget)
		{
			$result[$id] = unserialize($widget['code']);
			$result[$id]->id = $widget['id'];
			$result[$id]->name = $widget['name'];
			$result[$id]->description = $widget['description'];
			$result[$id]->template = $widget['template'];
		}

		return $result;
	}

	/**
	 * Получение списка всех виджетов
	 * 
	 * @return array array([ID] => array([ID], [TYPE], [NAME], [DESCRIPTION]), ...)
	 */
	public static function get_all_widgets()
	{
		return DB::select( 'id', 'type', 'name', 'description' )
			->from( 'widgets' )
			->order_by( 'type', 'asc' )
			->order_by( 'name', 'asc' )
			->execute()
			->as_array('id');
	}
	
	/**
	 * Получение списка виджетов по ID страницы
	 * 
	 * @cache Date::DAY
	 * @cache_key layout_blocks_[PAGE_ID]
	 * @param integer $page_id
	 * @return array @return array array([ID] => Model_Widget_Decorator, ....)
	 */
	public static function get_widgets_by_page( $page_id )
	{
		$res = DB::select('page_widgets.block', 'page_widgets.position')
			->select('widgets.*')
			->from('page_widgets')
			->join('widgets')
				->on('widgets.id', '=', 'page_widgets.widget_id')
			->where('page_id', '=', (int) $page_id)
			->order_by('page_widgets.block', 'ASC')
			->order_by('page_widgets.position', 'ASC')
			->cache_tags(array('layout_blocks'))
			->cache_key('layout_blocks_' . (int) $page_id)
			->cached(Date::DAY)
			->execute()
			->as_array('id');
		
		$widgets = array();
		foreach($res as $id => $widget)
		{
			$widgets[$id] = unserialize($widget['code']);
			$widgets[$id]->id = $widget['id'];
			$widgets[$id]->name = $widget['name'];
			$widgets[$id]->description = $widget['description'];
			$widgets[$id]->template = $widget['template'];
			$widgets[$id]->block = $widget['block'];
			$widgets[$id]->position = (int) $widget['position'];
		}
		
		return $widgets;
	}
	
	/**
	 * Копирование списка виджетов с одной страницы на другую
	 * 
	 * @param integer $from_page_id
	 * @param integer $to_page_id
	 * @return boolean
	 */
	public static function copy( $from_page_id, $to_page_id ) 
	{
		$widgets = DB::select('widget_id', 'block', 'position')
			->from('page_widgets')
			->where('page_id', '=', (int) $from_page_id)
			->execute()
			->as_array('widget_id');
		
		if(count($widgets) > 0)
		{
			$insert = DB::insert('page_widgets')
				->columns(array('page_id', 'widget_id', 'block', 'position'));
			
			foreach($widgets as $widget_id => $data)
			{
				$insert->values(array(
					'page_id' => (int) $to_page_id,
					'widget_id' => $widget_id,
					'block' => $data['block'],
					'position' => (int) $data['position']
				));
			}
			
			list($insert_id, $total_rows) = $insert->execute();
			
			return $total_rows;
		}
		
		return FALSE;
	}

	/**
	 * Добавление виджета в БД
	 *  
	 * @param Model_Widget_Decorator $widget
	 * @return integer ID виджета
	 * @throws HTTP_Exception_404
	 */
	public static function create( Model_Widget_Decorator $widget )
	{
		if( $widget->loaded() )
		{
			throw new HTTP_Exception_404( 'Widget created' );
		}

		$widget = ORM::factory('widget')
			->values( array(
				'type' => $widget->type,
				'name' => $widget->name,
				'description' => $widget->description,
				'code' => serialize($widget)
			))
			->create();

		return $widget->id;
	}

	/**
	 * Обновление виджета
	 * При обновлении виджета происходит вызов метода clear_cache() 
	 * для очистки кеша у виджета
	 * 
	 * @param Widget_Decorator $widget
	 * @return integer
	 * @throws HTTP_Exception_404
	 */
	public static function update( Model_Widget_Decorator $widget )
	{
		$orm_widget = ORM::factory('widget', $widget->id )
			->values(array(
				'type' => $widget->type,
				'name' => $widget->name,
				'template' => $widget->template,
				'description' => $widget->description,
				'code' => serialize($widget)
			))
			->update();
		
		$widget->clear_cache();

		return $orm_widget->id;
	}

	/**
	 * Удаление списка виджетов по ID
	 * 
	 * @param array $ids array([ID], [ID2])
	 * @return type
	 */
	public static function remove( array $ids )
	{
		return DB::delete( 'widgets' )
			->where( 'id', 'in', $ids )
			->execute();
	}

	/**
	 * Получение виджета по ID
	 * 
	 * @param integer $id
	 * @return Model_Widget_Decorator
	 */
	public static function load( $id )
	{
		$result = DB::select()
			->from( 'widgets' )
			->where( 'id', '=', (int) $id )
			->limit( 1 )
			->execute()
			->current();

		if( ! $result )
		{
			return NULL;
		}

		$widget = unserialize( $result['code'] );
		$widget->id = $result['id'];
		$widget->name = $result['name'];
		$widget->description = $result['description'];
		$widget->type = $result['type'];
		$widget->template = $result['template'];
		
		return $widget;
	}
	
	/**
	 * Размещение виджета на страницах
	 * 
	 * @param integer $widget_id Идентификатор
	 * @param array $data array([PAGE_ID] => [BLOCK NAME], ....)
	 * @observer widget_set_location
	 */
	public static function set_location($widget_id, array $data)
	{
		DB::delete('page_widgets')
			->where('widget_id', '=', (int) $widget_id)
			->execute();
		
		if( ! empty($data) )
		{
			$insert = DB::insert('page_widgets')
				->columns(array('page_id', 'widget_id', 'block', 'position'));

			$i = 0;
			foreach($data as $page_id => $block)
			{
				if($block['name'] == -1) continue;

				$insert->values(array(
					$page_id, (int) $widget_id, $block['name'], (int) $block['position']
				));

				$i++;
			}
			
			if( $i > 0 ) $insert->execute();
			
			Observer::notify( 'widget_set_location' );
		}
	}
	
	/**
	 * Обновление позици виджета на странице
	 * 
	 * При передачи названия блока есть два системных состояния
	 * 0 - Скрытый виджет
	 * -1 - Удалить со страницы
	 * 
	 * @param integer $page_id
	 * @param integer $widget_id
	 * @param array $data array(['block'] => [String], 'position' => [Integer])
	 */
	public static function update_location_by_page($page_id, $widget_id, array $data)
	{
		if( $data['block'] < 0 ) 
		{
			DB::delete('page_widgets')
				->where('widget_id', '=',$widget_id)
				->where('page_id', '=', $page_id)
				->execute();
		}
		else
		{
			DB::update('page_widgets')
				->where('widget_id', '=',$widget_id)
				->where('page_id', '=', $page_id)
				->set( array('block' => $data['block'], 'position' => (int) $data['position']) )
				->execute();
		}
		
		Observer::notify( 'widget_set_location' );
	}
	
	/**
	 * Усмтановка виджета из массива
	 * 
	 * array(
	 *		'type' => [Widget type],
	 *		'data' => array (
	 *			[KEY] => [VALUE]
	 *			.....
	 *		),
	 *		'blocks' => array (
	 *			[PAGE_ID] => [BLOCK NAME]
	 *		)
	 *	)
	 * 
	 * @param array $widget_array
	 * @return integer $id
	 */
	public static function install(array $widget_array)
	{
		if( 
			empty($widget_array['type']) 
		OR 
			empty($widget_array['data'])
		OR 
			empty($widget_array['data']['name'])) return;

		$widget = Widget_Manager::factory( $widget_array['type'] );
		
		try 
		{
			$widget->name = $widget_array['data']['name'];
			$widget->description = Arr::get($widget_array, 'description');
	
			$id = Widget_Manager::create($widget);
		}
		catch (Exception $e)
		{
			return FALSE;
		}
		
		$widget = Widget_Manager::load( $id );
		
		try 
		{
			$widget
				->set_values( $widget_array['data'] )
				->set_cache_settings( $widget_array['data'] );
	
			Widget_Manager::update($widget);
		}
		catch (Exception $e)
		{
			return FALSE;
		}
		
		$blocks = array();
		foreach (Arr::get($widget_array, 'blocks', array()) as $page_id => $block_name)
		{
			$blocks[$page_id] = array('name' => $block_name, 'position' => 500);
		}
		
		Widget_Manager::set_location($id, $blocks);
		
		return $id;
	}
}