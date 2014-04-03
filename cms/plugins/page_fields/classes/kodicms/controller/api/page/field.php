<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class KodiCMS_Controller_API_Page_Field extends Controller_System_Api {
	
	public function get_all()
	{
		$fields = ORM::factory('page_field')
			->group_by('title')
			->find_all()
			->as_array('id', 'title');
		
		$response = array();
		foreach ($fields as $id => $title)
		{
			$response[] = array(
				'id' => $id,
				'text' => $title
			);
		}
		
		$this->response($response);
	}

	public function rest_put()
	{
		$page_id = (int) $this->param('page_id', NULL, TRUE);
		$field_array = $this->param('field', NULL, TRUE);
		
		$page = ORM::factory('page', $page_id);
		
		if( ! $page->loaded() )
			throw new HTTP_API_Exception(__('Page not found'));
		
		if( !empty($field_array['field_id']))
		{
			$field = ORM::factory( 'page_field', (int) $field_array['field_id']);
			if( ! $field->loaded())
				throw new HTTP_API_Exception(__('Field not found'));
			
			$field_array['key'] = $field->key;
			$field_array['title'] = $field->title;
			
			unset($field_array['field_id'], $field);
		}

		$field_array['page_id'] = $page_id;
		
		$field = ORM::factory('page_field')->values($field_array);
		
		try {
			$field->create();
			
			$view = View::factory('page/fields/field', array(
				'field' => $field
			));
			
			$this->response((string) $view);
		} catch (ORM_Validation_Exception $v) {
			$this->json['message'] = $v->errors('validation');
		}
	}
	
	public function rest_delete()
	{
		$field_id = (int) $this->param('field_id', NULL, TRUE);
		
		ORM::factory('page_field', $field_id)->delete();
		
		$this->json['message'] = __('Page field deleted');
	}
	
	public function rest_post()
	{
		$field_id = (int) $this->param('field_id', NULL, TRUE);
		$value = $this->param('value');

		try {
			ORM::factory('page_field', $field_id)->values(array(
				'value' => $value
			))->update();
		} catch (ORM_Validation_Exception $v) {
			$this->json['message'] = $v->errors('validation');
		}		
	}
	
}