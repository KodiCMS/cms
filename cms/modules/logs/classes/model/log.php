<?php defined('SYSPATH') or die('No direct script access.');

class Model_Log extends ORM {

	/**
	 *
	 * @var array 
	 */
	protected $_sorting = array(
		'created_on' => 'desc'
	);
	
	/**
	 *
	 * @var array 
	 */
	protected $_serialize_columns = array(
		'additional'
	);
	
	/**
	 *
	 * @var array 
	 */
	protected $_load_with = array(
		'user'
	);
	
	/**
	 *
	 * @var array 
	 */
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'user'
		)
	);
	
	public function filter()
	{
		$request = Request::initial();
		
		$levels = (array) $request->query('level');
		
		if(!empty($levels))
		{
			$this->where('level', 'in', $levels);
		}
		
		$date_range = $request->query('created_on');
		if(empty($date_range))
		{
			$request->query('created_on', array(
				date('Y-m-d', strtotime("-1 month")), date('Y-m-d')
			));
		}
		
		if(is_array($date_range))
		{
			$this->where(DB::expr('DATE(created_on)'), 'between', $date_range);
		}
		else if(Valid::date($date_range))
		{
			$this->where(DB::expr('DATE(created_on)'), '=', $date_range);
		}
		
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function level()
	{
		return Log::level($this->level);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function user()
	{
		if(empty($this->user_id))
		{
			return NULL;
		}

		return HTML::anchor(Route::url('backend', array(
			'controller' => 'users', 
			'action' => 'profile', 
			'id' => $this->user->id
		)), $this->user->username);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function url()
	{
		return Arr::get($this->additional, ':url');
	}
	
	/**
	 * 
	 * @return string
	 */
	public function ip()
	{
		$ip =  Arr::get($this->additional, ':ip');
		
		return empty($ip) ? NULL : UI::label($ip, 'default');
	}
	
	public function clean_old()
	{
		return DB::delete($this->table_name())
			->where(DB::expr('DATE(created_on)'), '<', DB::expr('CURDATE() - INTERVAL 1 MONTH'))
			->execute($this->_db);
	}
}