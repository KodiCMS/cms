<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Widgets
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Widget extends Controller_System_API {

	public function get_list()
	{
		$page_widgets = DB::select('widget_id')
			->from('page_widgets')
			->where('page_id', '=', (int) $this->request->param('id'))
			->execute()
			->as_array('widget_id');

		$res_widgets = ORM::factory('widget');
		
		if(!empty($page_widgets))
			$res_widgets->where('id', 'NOT IN', $page_widgets);
		
		$res_widgets = $res_widgets->find_all();
		$widgets = array();
		
		foreach ($res_widgets as $widget)
		{
			$widgets[$widget->type()][$widget->id] = $widget;
		}
		
		$this->json = (string) View::factory( 'widgets/ajax/list', array(
			'widgets' => $widgets
		));
	}
	
	public function rest_put()
	{
		$widget_id = (int) $this->param('widget_id', NULL, TRUE);
		$page_id = (int) $this->param('page_id', NULL, TRUE);
		
		$data = array(
			'page_id' => $page_id, 
			'widget_id' => $widget_id
		);
		
		$insert = DB::insert('page_widgets')
			->columns( array_keys( $data ))
			->values( array_values( $data ))
			->execute();
		
		if($insert)
		{
			$this->response((string) View::factory( 'widgets/ajax/row', array(
				'widget' => Widget_Manager::load($widget_id)
			)));
			
			$this->message(__('Widget added to page'));
		}
	}
}