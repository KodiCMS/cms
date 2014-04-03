<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Datasources_Section extends Controller_System_Datasource
{	
	public function before()
	{
		if($this->request->action() != 'create')
		{
			$ds_id = (int) $this->request->param('id');
			$this->section($ds_id);

			if(Acl::check($this->section()->type().$ds_id.'.section.edit'))
			{
				$this->allowed_actions[] = 'edit';
			}
			
			if(Acl::check($this->section()->type().$ds_id.'.section.remove'))
			{
				$this->allowed_actions[] = 'remove';
			}
		}

		parent::before();
	}

	public function action_create()
	{
		$type = $this->request->param('id');
		$type = strtolower($type);

		$types = Datasource_Data_Manager::types();
		if( Arr::get($types, $type) === NULL)
		{
			throw new Kohana_Exception('Datasource type :type not found', array(':type' => $type));
		}
		
		if($this->request->method() === Request::POST)
		{
			return $this->_create($type);
		}
		
		$this->template->title = __('Add section :type', array(':type' => Arr::get($types, $type)));

		$this->breadcrumbs
				->add($this->template->title);
		
		try
		{
			$this->template->content = View::factory('datasource/'.$type.'/section/create', array(
				'type' => $type,
				'data' => Flash::get('post_data')
			));
		} 
		catch (Exception $exc)
		{
			$this->template->content = View::factory('datasource/section/create', array(
				'type' => $type,
				'data' => Flash::get('post_data')
			));
		}
	}
	
	/**
	 * 
	 * @param string $type
	 */
	private function _create($type)
	{
		$section = Datasource_Section::factory($type);
		
		try
		{
			$ds_id = $section->create($this->request->post());
		}
		catch (Validation_Exception $e)
		{
			Messages::errors($e->errors('validation'));
			$this->go_back();
		}
		
		Messages::success( __( 'Datasource has been saved!' ) );

		$this->go( Route::get('datasources')->uri(array(
			'directory' => 'datasources',
			'controller' => 'section',
			'action' => 'edit',
			'id' => $ds_id
		)));
	}

	public function action_edit()
	{
		if($this->request->method() === Request::POST)
		{
			return $this->_edit($this->section());
		}
		
		$this->template->title = __('Edit section :name', array(
			':name' => $this->section()->name
		));
		
		$this->breadcrumbs
			->add($this->section()->name, Route::get('datasources')->uri(array(
				'controller' => 'data',
				'directory' => 'datasources',
			)) . URL::query(array('ds_id' => $this->section()->id()), FALSE))
			->add($this->template->title);
		
		try
		{
			$this->template->content = View::factory('datasource/'.$this->section()->type().'/section/edit', array(
				'ds' => $this->section()
			));
		} 
		catch (Exception $exc)
		{
			$this->template->content = View::factory('datasource/section/edit', array(
				'ds' => $this->section()
			));
		}
	}
	
	/**
	 * 
	 * @param Datasource_Section $ds
	 */
	private function _edit($ds)
	{
		try
		{
			$ds->save($this->request->post());
		}
		catch (Validation_Exception $e)
		{
			Messages::errors($e->errors('validation'));
			$this->go_back();
		}
		
		Messages::success( __( 'Datasource has been saved!' ) );

		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go( Route::get('datasources')->uri(array(
				'directory' => 'datasources',
				'controller' => 'data'
			)) .  URL::query(array('ds_id' => $ds->id()), FALSE));
		}
		else
		{
			$this->go_back();
		}
	}
	
	public function action_remove()
	{
		$this->section()->remove();

		Messages::success(__('Datasource has been deleted!'));
		$this->go_back();
	}
}