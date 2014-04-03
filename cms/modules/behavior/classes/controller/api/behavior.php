<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Behavior
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Behavior extends Controller_System_Api {

	public function get_settings()
	{
		$id = $this->param('id', NULL );
		$page_id = $this->param('page_id', NULL, TRUE);
		
		if(empty($id)) return;
		
		$page = ORM::factory('page', (int) $page_id);
		
		if ( ! $page->loaded() )
		{
			throw new HTTP_Exception_404('Page :id not found!', array(
				':id' => $page_id));
		}
		
		$page->behavior_id = $id;
		
		try
		{
			$behavior = new Behavior_Settings($page);
			
			$this->response( (string) $behavior->render() );

		} catch (Kohana_Exception $e) {}
	}
}