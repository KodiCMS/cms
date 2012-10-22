<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Datasources_Data extends Controller_System_Datasource
{
	public $template = 'datasource/template';
	public function action_index()
	{
		$cur_ds_id = (int) Arr::get($_GET, 'ds_id', Cookie::get('ds_id'));
		$tree = Datasource_Manager::get_tree();

		$cur_ds_id = Datasource_Manager::exists($cur_ds_id) 
				? $cur_ds_id
				: Datasource_Manager::$first;
		
		$ds = Datasource_Manager::load($cur_ds_id);
		
		$this->template->content = View::factory('datasource/data/index');
		$this->template->menu = View::factory('datasource/data/menu', array(
			'tree' => Datasource_Manager::get_tree(),
		));
		
		$this->template->toolbar = View::factory('datasource/data/toolbar');
		
		if($ds) 
		{
			$this->breadcrumbs
				->add($ds->name);

			Cookie::set('ds_id', $cur_ds_id);
			$this->template->content->headline = View::factory('datasource/data/' . $ds->ds_type . '/headline', array(
				'fields' => $ds->fields(),
				'data' => $ds->get_headline( $cur_ds_id )
			));

			
			$this->template->set_global(array(
				'ds_type' => $ds->ds_type,
				'ds_id' => $cur_ds_id
			));
		}
		else
		{
			$this->template->set_global(array(
				'ds_type' => NULL,
				'ds_id' => $cur_ds_id
			));
			
			
			$this->template->content = NULL;
		}
		
		$this->styles[] = ADMIN_RESOURCES . 'libs/jquery-treeview/jquery.treeview.css';
		$this->scripts[] = ADMIN_RESOURCES . 'libs/jquery-treeview/jquery.treeview.js';
		
		$agent = Datasource_Hybrid_Agent::instance(1, 1, FALSE);
	}
}