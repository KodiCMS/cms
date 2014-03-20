<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class KodiCMS_Controller_Update extends Controller_System_Backend {
	
	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('Update'), Route::url('backend', array('controller' => 'update')));
	}
	
	public function action_index() 
	{
		Assets::package('ace');
		
		$this->template->title = __('Update');
		
		$db_sql = Database_Helper::schema();
		$file_sql = Database_Helper::install_schema();

		$compare = new Database_Helper;
		$diff = $compare->get_updates($db_sql, $file_sql, TRUE);
		
		$this->template->content = View::factory( 'update/index', array(
			'actions' => $diff,
		) );
	}
	
	public function action_patch()
	{
		if($this->request->method() === Request::POST)
		{
			return $this->_apply_patch();
		}

		$patches_list = Kohana::list_files('patches', array(DOCROOT));
		
		$patches = array();
		foreach ($patches_list as $path)
		{
			$patches[$path] = pathinfo($path, PATHINFO_FILENAME);
		}
		
		$this->template->content = View::factory( 'update/patches', array(
			'patches' => $patches,
		) );
	}
	
	private function _apply_patch()
	{
		$patch = $this->request->post('patch');
		
		if(file_exists($patch))
		{
			try
			{
				include $patch;
			} 
			catch (Kohana_Exception $ex) 
			{
				Messages::errors($ex->getMessage());
				$this->go_back();
			}
			
			@unlink($patch);
		}
		
		$this->go_back();
	}
}