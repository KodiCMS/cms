<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Elfinder
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Elfinder extends Controller_System_Template {
	
	public $template = 'system/frontend';
	
	public function before()
	{
		parent::before();
		
		Assets::js('jquery', ADMIN_RESOURCES . 'libs/jquery.min.js');
		Assets::package(array('elfinder', 'jquery-ui', 'backbone'));

		Assets::js('global', ADMIN_RESOURCES . 'js/backend.js', 'backbone');
	}
	
	public function action_index()
	{
		$this->template->content = View::factory('elfinder/manager');
	}
}