<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Tag extends Record
{
    const TABLE_NAME = 'tags';
	const SEPARATOR = ',';
	
//	protected $_primary_key = 'name';
	
	public static function findAllLike($tag)
	{
		return Record::findAllFrom(static::calledClass(), array(
			'or_where' => array(
				array('name', 'like', '%:query%')
			),
			'order_by' => array(
				array('count', 'desc'),
			)
		), 
		array(
			':query' => DB::expr($tag)
		));
	}
}