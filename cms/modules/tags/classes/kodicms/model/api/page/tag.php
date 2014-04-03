<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Model/Api
 * @author		ButscHSter
 */
class KodiCMS_Model_API_Page_Tag extends Model_API {
	
	protected $_table_name = 'tags';

	public function get_all($uids, $fields = array(), $page_id = NULL)
	{
		$uids = $this->prepare_param($uids, array('Valid', 'numeric'));
		$fields = $this->prepare_param($fields);

		$tags = DB::select('id', 'name')
			->select_array( $this->filtered_fields( $fields ) )
			->from($this->table_name());
		
		if(!empty($uids))
		{
			$tags->where('id', 'in', $uids);
		}
		
		if($page_id !== NULL)
		{
			$tags
				->join('page_tags', 'left')
				->on('page_tags.tag_id', '=', $this->table_name() . '.id')
				->where('page_tags.page_id', '=', (int) $page_id);
		}
		
		return $tags
			->cache_tags( array('page_tags') )
			->cached( (int) Config::get('cache', 'tags') )
			->execute()
			->as_array();
	}
	
}