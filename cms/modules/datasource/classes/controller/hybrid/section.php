<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Hybrid_Section extends Controller_System_Datasource
{

	public function action_create()
	{
		if($this->request->method() === Request::POST)
		{
			return $this->_create();
		}

		$dss = Datasource_Manager::get_all( Datasource_Manager::DS_HYBRID );
		
		$options = array(0 => __('None'));
		
		foreach ($dss as $ds_id => $ds)
		{
			$options[$ds_id] = $ds['name'];
		}

		$this->breadcrumbs
				->add(__('Add hybrid'));
		
		$this->template->content = View::factory('datasource/data/hybrid/create', array(
			'options' => $options
		));
	}
	
	private function _create()
	{
		$dsf = new Datasource_Hybrid_Factory();
		
		$array = Validation::factory($this->request->post())
			->rules('ds_key', array(
				array('not_empty')
			))
			->rules('ds_name', array(
				array('not_empty')
			))
			->label( 'ds_name', __('Header') );
		
		if(!$array->check())
		{
			Messages::errors($array->errors('validation'));
			$this->go_back();
		}
		
		$result = $dsf->create($array['ds_key'], $array['ds_name'], $array['ds_description'], $array['ds_parent']);
		
		if($result !== NULL)
		{
			$this->go( URL::site('hybrid/section/edit/' . $result->ds_id));
		}
		else
		{
			$this->go_back();
		}
	}

	public function action_edit()
	{
		$ds_id = (int) $this->request->param('id');

		$ds = Datasource_Manager::load($ds_id);
		
		if($this->request->method() === Request::POST)
		{
			return $this->_edit($ds);
		}

		$this->breadcrumbs
			->add(__('Edit hybrid'));
		
		$this->template->content = View::factory('datasource/data/hybrid/edit', array(
			'record' => $ds->get_record(),
			'ds' => $ds
		));
	}
	
	private function _edit($ds)
	{
		$array = Validation::factory($this->request->post())
			->rules('ds_name', array(
				array('not_empty')
			))
			->label( 'ds_name', __('Header') );
		
		if(!$array->check())
		{
			Messages::errors($array->errors('validation'));
			$this->go_back();
		}
		
		$ds->name = $this->request->post('ds_name');
		$ds->description = $this->request->post('ds_description');	
		
		
		$ds->save();

		// save and quit or save and continue editing?
		if ( $this->request->post('commit') )
		{
			$this->go( URL::site('datasources/data' . URL::query(array('ds_id' => $ds->ds_id), FALSE)));
		}
		else
		{
			$this->go_back();
		}
	}
	
	public function action_remove()
	{
		$ds_id = (int) $this->request->param('id');
		
		$dsf = new Datasource_Hybrid_Factory();
		
		$dsf->remove($ds_id);
		$this->go_back();
	}
}