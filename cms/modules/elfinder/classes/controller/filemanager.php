<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Elfinder
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_FileManager extends Controller_System_Backend {
	
	public function action_index()
	{
		$this->template->title = __('File manager');
		$this->breadcrumbs
			->add($this->template->title, $this->request->controller());

		Assets::package(array('elfinder', 'jquery-ui'));

		$this->template->content = View::factory('elfinder/filemanager');
	}
}