<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Datasource
 */
class Datasource_Data_Manager {
	
	/**
	 *
	 * @var integer
	 */
	public static $first_section = NULL;
	
	/**
	 * 
	 * @return array
	 */
	public static function types()
	{
		return Config::get('datasources.types');
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get_tree( $type = NULL )
	{
		$result = array();
		
		$query = self::get_all($type);

		foreach ( $query as $r )
		{
			if( self::$first_section === NULL ) self::$first_section = $r['id'];

			$result[$r['type']][$r['id']] = array(
				'name' => $r['name'], 
				'description' => $r['description']
			);
		}
		
		return $result;
	}
	
	/**
	 * @param	string	$type	Datasource type
	 * 
	 * @return	array
	 */
	public static function get_all($type = NULL) 
	{
		if(is_array($type) AND empty($type)) return array();

		$sections = DB::select('id', 'name', 'type', 'description')
			->from(array('datasources', 'ds'))
			->order_by('type')
			->order_by('name');
		
		if($type !== NULL)
		{
			$sections->where('ds.type', is_array($type) ? 'IN' : '=', $type);
		}

		return $sections
			->execute()
			->as_array('id');
	}
	
	/**
	 * 
	 * @param integer $ds_id	Datasource ID
	 * 
	 * @return boolean
	 */
	public static function exists($ds_id) 
	{
		return (bool) DB::select('id')
			->from('datasources')
			->where('id', '=', (int) $ds_id)
			->limit(1)
			->execute()
			->get('id');
	}
	
	/**
	 * 
	 * @param integer $ds_id Datasource ID
	 * @return array
	 */
	public static function get_info($ds_id) 
	{
		return DB::select('id', 'type', 'name', 'description')
			->from(array('datasources', 'ds'))
			->where('ds.id', '=', (int) $ds_id)
			->limit(1)
			->execute()
			->current();
	}

	/**
	 * @param indeger $ds_id Datasource ID
	 * @return Datasource_Section|DataSource_Data_Hybrid_Section
	 */
	public static function load($ds_id) 
	{
		return Datasource_Section::load($ds_id);
	}
	
	
	/**
	 * 
	 * @param integer $ds_id
	 * @return array
	 */
	public static function clear_cache( $ds_id, array $widget_types = array() ) 
	{
		$objects = Widget_Manager::get_widgets($widget_types);
		
		$cleared_ids = array();
	
		foreach($objects as $id => $data)
		{
			$widget = Widget_Manager::load($id);
			if($widget->ds_id == $ds_id )
			{
				$cleared_ids[] = $widget->id;
				$widget->clear_cache();
			}
		}
		
		return $cleared_ids;
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get_all_indexed() 
	{
		return DB::select('id', 'type', 'name', 'description')
			->from('datasources')
			->where('indexed', '!=', 0)
			->order_by('name')
			->execute()
			->as_array('id');
	}
}