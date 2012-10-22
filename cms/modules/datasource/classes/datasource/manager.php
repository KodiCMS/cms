<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Manager {
	
	const DS_HYBRID = 'hybrid';
	
	/**
	 *
	 * @var integer
	 */
	public static $first = NULL;
	
	/**
	 * 
	 * @return array
	 */
	public static function types()
	{
		return array(
			self::DS_HYBRID => __('Hybrid ')
		);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get_tree()
	{
		$result = array();
		$dsh = array();
		
		$query = DB::select(array('ds.ds_id', 'id'), array('ds_type', 'type'), 'name', 'parent')
			->from(array('datasources', 'ds'))
			->join(array('hybriddatasources', 'hds'), 'left')
				->on('ds.ds_id', '=', 'hds.ds_id')
			->where('internal', '=', 0)
			->order_by('ds_type')
			->order_by('ds_key')
			->order_by('name')
			->cached()
			->execute()
			->as_array('id');

		foreach ( $query as $r )
		{
			if( ! self::$first ) self::$first = $r['id'];

			if($r['type'] == self::DS_HYBRID)
			{
				if($r['parent'] == 0) 
				{
					$result[$r['type']][$r['id']] = $r['name'];
			
					$dsh[$r['id']] = & $result[$r['type']][$r['id']];
				} 
				else 
				{
					if(is_array($dsh[$r['parent']]))
					{
						$dsh[$r['parent']][$r['id']] = $r['name'];
					}
					else 
					{
						$name = $dsh[$r['parent']];
						$dsh[$r['parent']] = array(
							$name => array($r['id'] => $r['name'])
						);
						
						$dsh[$r['parent']] = & $dsh[$r['parent']][$name];
					}

					$dsh[$r['id']] = & $dsh[$r['parent']][$r['id']];
				}
			} 
			else
			{
				$result[$r['type']][$r['id']] = $r['name'];
			}
		}
		
		return $result;
	}
	
	/**
	 * @param	string	$type	Datasource type
	 * 
	 * @return	array
	 */
	public static function get_all($type) 
	{
		return DB::select(array('ds.ds_id', 'id'), 'name', 'description')
			->select('parent', 'ds_key', 'path', 'internal')
			->from(array('datasources', 'ds'))
			->join(array('hybriddatasources', 'hds'), 'left')
				->on('ds.ds_id', '=', 'hds.ds_id')
			->where('ds.ds_type', is_array($type) ? 'IN' : '=', $type)
			->where('internal', '=', 0)
			->order_by('ds_key')
			->order_by('name')
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
		return (bool) DB::query(Database::SELECT, '
			SELECT ds_id FROM datasources WHERE ds_id = :ds_id LIMIT 1
		')
			->param(':ds_id', (int) $ds_id)
			->execute()
			->get('ds_id');
	}
	
	/**
	 * 
	 * @param integer $ds_id Datasource ID
	 * @return array
	 */
	public static function get_info($ds_id) 
	{
		return DB::query(Database::SELECT, '
			SELECT ds.ds_id AS id, ds_type AS type, name, description, internal, parent, ds_key, path
			FROM datasources ds LEFT JOIN hybriddatasources hds ON (ds.ds_id = hds.ds_id)
			WHERE ds.ds_id = :ds_id)
			LIMIT 1
		')
			->param(':ds_id', (int) $ds_id)
			->execute()
			->current();
	}
	
	/**
	 * @param indeger $ds_id Datasource ID
	 * @return Datasource_Section|Datasource_Hybrid_Section
	 */
	public static function load($ds_id) 
	{
		return Datasource_Section::load($ds_id);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get_all_indexed() 
	{
		return DB::query(Database::SELECT, '
			SELECT ds_id AS id, ds_type as type, name, description
			FROM datasources
			WHERE internal = 0 AND indexed != 0
			ORDER BY name
		')
			->cached()
			->execute()
			->as_array('id');
	}
}