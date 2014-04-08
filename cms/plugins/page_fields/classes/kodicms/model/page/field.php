<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Page_Field extends ORM {
	
	public function labels()
	{
		return array(
			'title' => __('Field title'),
			'key' => __('Field key'),
			'value' => __('Field value')
		);
	}

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 32))
			),
			'key' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 50))
			),
		);
	}
	
	public function filters()
	{
		return array(
			'key' => array(
				array('URL::title', array(':value', '_'))
			)
		);
	}
	
	public function get_by_page_id($page_id)
	{
		return $this
			->where('page_id', '=', (int) $page_id)
			->find_all();
	}
	
	public static function copy( $from_page_id, $to_page_id ) 
	{
		$fields = DB::select()
			->from('page_fields')
			->where('page_id', '=', (int) $from_page_id)
			->execute()
			->as_array();
		
		if(count($fields) > 0)
		{
			$insert = DB::insert('page_fields')
				->columns(array('page_id', 'title', 'key', 'value'));
			
			foreach($fields as $field)
			{
				unset($field['id']);

				$field['page_id'] = (int) $to_page_id;
				$insert->values($field);
			}
			
			list($insert_id, $total_rows) = $insert->execute();
			
			return $total_rows;
		}
		
		return FALSE;
	}
}