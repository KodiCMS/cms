<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Datasources_Data extends Controller_System_Datasource
{
	public function action_index()
	{
		$cur_ds_id = (int) Arr::get($this->request->query(), 'ds_id', Cookie::get('ds_id'));
		$tree = Datasource_Data_Manager::get_tree();

		$ds_id = $cur_ds_id;

		$cur_ds_id = Datasource_Data_Manager::exists($cur_ds_id) 
				? $cur_ds_id
				: Datasource_Data_Manager::$first_section;
		
		$ds = $this->section($cur_ds_id);
		
		$this->template->content = View::factory('datasource/content', array(
			'content' => View::factory('datasource/data/index'),
			'menu' => View::factory('datasource/data/menu', array(
				'tree' => $tree,
			))
		));

		$this->template->footer = NULL;
		$this->template->breadcrumbs = NULL;
		
		if ($ds instanceof Datasource_Section)
		{
			$this->set_title($ds->name);

			$limit = (int) Arr::get($this->request->query(), 'limit', Cookie::get('limit'));

			Cookie::set('ds_id', $cur_ds_id);

			$keyword = $this->request->query('keyword');

			if (!empty($limit))
			{
				Cookie::set('limit', $limit);
				$this->section()->headline()->limit($limit);
			}

			$selected = ($cur_ds_id == $ds_id) ? 'active' : '';

			$this->template->content->content->headline = $this->section()->headline()->render();
			$this->template->content->content->toolbar = View::factory('datasource/' . $ds->type() . '/toolbar', array(
				'keyword' => $keyword
			));

			$this->template->set_global(array('datasource' => $ds, 'selected' => $selected));
		}
		else
		{
			$this->template->content->content = NULL;
		}
	}
}