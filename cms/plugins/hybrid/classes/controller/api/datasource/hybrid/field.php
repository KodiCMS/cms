<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Api_Datasource_Hybrid_Field extends Controller_System_API
{
	public function rest_delete()
	{
		$ds_id = (int) $this->request->post('ds_id');
		
		$fields = $this->request->post('field');
		
		$ds = Datasource_Data_Manager::load($ds_id);
		DataSource_Hybrid_Field_Factory::remove_fields($ds->record(), $fields);
		
		$this->response($fields);
	}
}