<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Datasources_Data extends Controller_System_Datasource
{
	public $template = 'datasource/template';

	public function action_index()
	{
		$cur_ds_id = (int) Arr::get($this->request->query(), 'ds_id', Cookie::get('ds_id'));
		$tree = Datasource_Data_Manager::get_tree();

		$cur_ds_id = Datasource_Data_Manager::exists($cur_ds_id) 
				? $cur_ds_id
				: Datasource_Data_Manager::$first_section;
		
		$ds = $this->section($cur_ds_id);
		
		$this->template->content = View::factory('datasource/data/index');
		$this->template->menu = View::factory('datasource/data/menu', array(
			'tree' => $tree,
		));
		
		if($ds) 
		{
			$this->template->title = $ds->name;

			$this->breadcrumbs
				->add($this->template->title);
			
			$limit = (int) Arr::get($this->request->query(), 'limit', Cookie::get('limit'));
			
			
			Cookie::set('ds_id', $cur_ds_id);
			
			$keyword = $this->request->query('keyword');
			
			if(!empty($limit))
			{
				Cookie::set('limit', $limit);
				$this->section()->headline()->limit($limit);
			}
	
			$this->template->content->headline = $this->section()->headline()->render();
			
			$this->template->content->toolbar = View::factory('datasource/' . $ds->type() . '/toolbar', array(
				'keyword' => $keyword
			));
			
			$this->template->set_global(array(
				'ds_type' => $ds->type(),
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
	}
}