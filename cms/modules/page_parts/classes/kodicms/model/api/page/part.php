<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Model/Api
 * @author		ButscHSter
 */
class KodiCMS_Model_API_Page_Part extends Model_API {
	
	protected $_table_name = 'page_parts';
	
	protected $_secured_columns = array(
		'content', 'content_html'
	);
	
	public function get_all($page_id, $fields = array())
	{
		$fields = $this->prepare_param($fields);

		$parts = DB::select('id', 'name')
			->select_array( $this->filtered_fields( $fields ) )
			->from($this->table_name());

		$parts = $parts
			->where('page_id', '=', (int) $page_id)
			->cache_tags( array('page_parts') )
			->cached((int) Config::get('cache', 'page_parts'))
			->execute()
			->as_array();
		
		$is_developer = (int) AuthUser::hasPermission( 'administrator, developer' );
		
		foreach ($parts as & $part)
		{
			$part['is_developer'] = $is_developer;
		}
		
		return $parts;
	}
}