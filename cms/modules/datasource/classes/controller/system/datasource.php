<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_System_Datasource extends Controller_System_Backend
{
	public $route = 'datasources';
	
	public function before()
	{
		parent::before();

		$this->scripts[] = ADMIN_RESOURCES . 'js/ds.js';
		
		$this->breadcrumbs
			->add(__('Datasources'), 'datasources/data');
	}
}