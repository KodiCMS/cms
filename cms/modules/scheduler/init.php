<?php defined('SYSPATH') or die('No direct access allowed.');

Observer::observe('system::init', function() {
	if (Config::get('job', 'agent', Model_Job::AGENT_SYSTEM) === Model_Job::AGENT_CRON)
		return;

	try
	{
		ORM::factory('job')->run_all();
	} 
	catch (Exception $ex) 
	{

	}
	
});

Observer::observe('view_setting_plugins', function() {
	echo View::factory('scheduler/settings_page');
});

Observer::observe('scheduler_callbacks', function() {
	scheduler::add(function($from, $to) {
		$from = date('Y-m-d', $from);
		$to = date('Y-m-d', $to);

		$jobs = ORM::factory('job')
				->where(DB::expr('DATE(date_next_run)'), 'between', array($from, $to))
				->find_all();

		$data = array();
		foreach ($jobs as $job)
		{
			$data[] = array(
				'title' => $job->name,
				'start' => strtotime($job->date_next_run),
				'url' => Route::url('backend', array(
					'controller' => 'scheduler', 'action' => 'edit',
					'id' => $job->id
				)),
				'allDay' => FALSE
			);
		}
		return $data;
	});
});
