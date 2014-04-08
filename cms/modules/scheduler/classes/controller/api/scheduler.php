<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Scheduler
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Scheduler extends Controller_System_Api {
	
	public function before() 
	{
		parent::before();
	}
	
	public function rest_get()
	{
		$from = (int) $this->param('from', NULL, TRUE);
		$to = (int) $this->param('to', NULL, TRUE);

		$this->response(scheduler::get($from, $to));
	}
}