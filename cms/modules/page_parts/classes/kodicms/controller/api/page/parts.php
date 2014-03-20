<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class KodiCMS_Controller_API_Page_Parts extends Controller_System_Api {
	
	public function get_get()
	{		
		$page_id = $this->param('pid', NULL, TRUE);
		
		$parts = Model_API::factory('api_page_part')
			->get_all($page_id, $this->fields);

		$this->response($parts);
	}
	
	public function rest_get()
	{
		return $this->get_get();
	}
	
	public function rest_put()
	{
		$id = $this->param('id', NULL, TRUE);
		$part = Record::findByIdFrom('Model_Page_Part', (int) $id);
		
		$part
			->setFromData($this->params(), array('id'))
			->save();
		
		$part = $part->prepare_data();
		$part['is_developer'] = (int) AuthUser::hasPermission( 'administrator, developer' );
		
		if(Kohana::$caching === TRUE)
		{
			Cache::instance()->delete_tag('page_parts');
		}

		$this->response($part);
	}
	
	public function rest_post()
	{
		$part = new Model_Page_Part;

		$params = $this->params();
		$params['filter_id'] = Config::get('site', 'default_filter_id');
		
		$part
			->setFromData($params)
			->save();

		$part = $part->prepare_data();
		$part['is_developer'] = (int) AuthUser::hasPermission( 'administrator, developer' );
		
		if(Kohana::$caching === TRUE)
		{
			Cache::instance()->delete_tag('page_parts');
		}

		$this->response($part);
	}
	
	public function rest_delete()
	{
		$id = $this->param('id', NULL, TRUE);
		
		$part = Model_Page_Part::findByIdFrom( 'Model_Page_Part', (int) $id );
		
		if(Kohana::$caching === TRUE)
		{
			Cache::instance()->delete_tag('page_parts');
		}

		$part->delete();
	}
}