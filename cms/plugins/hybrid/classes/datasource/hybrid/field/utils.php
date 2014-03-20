<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Hybrid_Field_Utils {
	
	/**
	 * Кеш заголовков документов
	 * 
	 * @static
	 * @var array 
	 */
	protected static $_cached_headers = array();
	
	/**
	 * @deprecated since 10.0.0
	 * 
	 * @param integer $ds_id
	 * @return Datasource_Section
	 */
	public static function load_ds($ds_id)
	{
		return Datasource_Data_Manager::load($ds_id);
	}
	
	/**
	 * Получение заголовка документа
	 * 
	 * @param integer $ds_id Идентификатор раздела
	 * @param integer $id Идентификатор документа
	 * @return string
	 */
	public static function get_document_header( $ds_id, $id ) 
	{
		if(isset(self::$_cached_headers[$ds_id][$id]))
		{
			return self::$_cached_headers[$ds_id][$id];
		}

		$documents = self::get_document_headers( $ds_id, array($id));
		
		return count($documents) > 0 ? reset($documents) : NULL;
	}
	
	/**
	 * Получение списка заголовков документов
	 * 
	 * @param integer $ds_id
	 * @param array $ids
	 * @return array
	 */
	public static function get_document_headers( $ds_id, array $ids ) 
	{
		if(empty($ids)) return array();
		
		$result = DB::select('id', 'header')
			->from('dshybrid')
			->where('id', 'in', $ids)
			->execute()
			->as_array('id', 'header');

		foreach ($result as $id => $header )
		{
			self::$_cached_headers[$ds_id][$id] = $header;
		}
		
		return $result;
	}
}