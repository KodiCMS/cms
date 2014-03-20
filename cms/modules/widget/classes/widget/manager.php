<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Widgets
 * @author		ButscHSter
 */
class Widget_Manager {

	/**
	 * 
	 * @return array
	 */
	public static function map()
	{
		return Kohana::$config->load( 'widgets' )->as_array();
	}
	
	/**
	 * 
	 * @param string $type
	 * @return Model_Widget_Decorator
	 */
	public static function get_empty_object( $type )
	{
		$class = 'Model_Widget_' . $type;

		if( ! class_exists($class) ) return NULL;
	
		$widget = new $class;

		return $widget;
	}
	
	/**
	 * 
	 * @param string $type
	 * @return array
	 */
	public static function get_widgets( $type )
	{
		$result = array( );
		
		if(!is_array($type)) $type = array($type);

		$res = DB::select( 'w.id', 'w.name', 'w.description', 'w.created_on', 'w.type' )
				->select( array( DB::expr( 'COUNT(:table)' )->param(
							':table', Database::instance()->quote_column( 'pw.page_id' ) ), 'used' ) )
				->from( array( 'widgets', 'w' ) )
				->join( array( 'page_widgets', 'pw' ), 'left' )
				->on( 'w.id', '=', 'pw.widget_id' )
				->group_by( 'w.id' )
				->group_by( 'w.name' )
				->order_by( 'w.name' );
		
		if(!empty($type))
		{
			$res->where( 'w.type', 'in', $type );
		}
		
		$res = $res->execute();

		foreach( $res as $row )
		{
			$result[$row['id']] = array(
				'name' => $row['name'],
				'description' => $row['description'],
				'is_used' => $row['used'] > 0,
				'date' => $row['created_on']
			);
		}

		return $result;
	}

	/**
	 * @return array
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
	 * 
	 * @param integer $id
	 * @return type
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
	 * 
	 * @param Model_Widget_Decorator $widget
	 * @return integer
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
	 * 
	 * @param array $ids
	 * @return type
	 */
	public static function remove( array $ids )
	{
		return DB::delete( 'widgets' )
			->where( 'id', 'in', $ids )
			->execute();
	}

	/**
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
	 * 
	 * @param integer $widget_id
	 * @param array $data
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

		$widget = Widget_Manager::get_empty_object( $widget_array['type'] );
		
		if( $widget === NULL ) return FALSE;
		
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