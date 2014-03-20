<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Logs
 * @category	Api
 * @author		ButscHSter
 */
class Controller_API_Log extends Controller_System_Api {
	
	public function post_clear_old()
	{
		ORM::factory('log')->clean_old();
		
		$this->response((bool) $delete);
		$this->message(__('Old logs has been deleted'));
	}

	public function get_get()
	{		
		$uids = $this->param('uids');
		$level = $this->param('level');
		$from = $this->param('from');
		$to = $this->param('to');
		
		$limit = (int) $this->param('to', 10);
		
		if($limit > 100)
		{
			$limit == 100;
		}
		
		if( ! empty($uids))
		{
			$uids = explode(',', $uids);
		}
		
		if( ! empty($level))
		{
			$level = explode(',', $level);
		}
		else
		{
			$level = array(Log::INFO);
		}

		$list = DB::select('logs.id', 'logs.created_on', 'logs.level', 'logs.message', 'logs.user_id')
			->select('users.email', 'users.username')
			->from('logs')
			->join('users')
				->on('users.id', '=', 'logs.user_id')
			->limit($limit)
			->order_by('created_on', 'desc');
		
		if(!empty($from) AND !empty($to))
		{
			$list->where(DB::expr('DATE(created_on)'), 'between', array($from, $to));
		}
		
		if(!empty($uids))
		{
			$list->where('user_id', 'in', $uids);
		}
		
		if(!empty($level))
		{
			$list->where('level', 'in', $level);
		}

		$this->response($list->execute()->as_array('id'));
	}
}