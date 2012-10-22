<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field_Utils {
	
	public static function create_internal_ds($name, $type, $orig_id = NULL)
	{
		$ds = NULL;
		$class_name = 'Datasource_' . $type . '_Object';
		
		if( ! class_exists( $class_name ))
		{
			throw new Kohana_Exception('Class :class_name not exists', array(
				':class_name' => $class_name ));
		}

		$ds = new $class_name;
		$ds->create($name, $name.' (Internal Datasource)', 1);
		
		if($orig_id !== NULL) 
		{
			$prototype = self::load_ds($orig_id);
			
			$ds->copy_props($prototype);
	
			$ds->save();
		}

		return $ds->ds_id;
	}
	
	public static function load_ds($ds_id)
	{
		return Datasource_Manager::load($ds_id);
	}
	
	public static function get_document_header($type, $ds_id, $id) 
	{		
		$header = '';

		$result = self::_query_document_headers($type, $ds_id, array($id));
		if($result)
		{
			$header = $result['header'];
		}

		return $header;
	}
	
	public static function get_document_headers($type, $ds_id, array $ids) 
	{
		$result = array();
		if(count($ids) > 0) 
		{
			$result = self::_query_document_headers($type, $ds_id, $ids);
			foreach ($result as $r)
			{
				$result[ $r['id']] = $r['header'];
			}
		}
		return $result;
	}
	
	protected static function _query_document_headers($type, $ds_id, array $ids) 
	{
		$query = DB::select('dshybrid.id', 'dshybrid.header')
			->from(array('ds' . $type . '_' . $ds_id, 'ds'))
			->join('dshybrid', 'left')
				->on('dshybrid.id', '=', 'ds.id')
			->where('ds.id', 'in', $ids)
			->execute();
		
		if(count($ids) == 1)
		{
			return $query->current();
		}

		return $query;
	}
}