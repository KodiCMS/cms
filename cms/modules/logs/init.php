<?php defined('SYSPATH') or die('No direct access allowed.');

Observer::observe('view_user_profile_information', function($user_id) {
	$logs = Api::get('log.get', array('uids' => $user_id, 'level' => Log::INFO))->as_object()->get('response');
	echo View::factory('log/profile/activity', array(
		'user_id' => (int) $user_id,
		'logs' => $logs
	));
});

Observer::observe('view_setting_plugins', function() {
	echo View::factory('logs/settings_page');
});


//Observer::observe('scheduler_callbacks', function() {
//	scheduler::add(function($from, $to) {
//		$from = date('Y-m-d', $from);
//		$to = date('Y-m-d', $to);
//
//		$logs = Api::get('log.get', array(
//			'uids' => AuthUser::getId(), 
//			'level' => Log::INFO, 
//			'from' => $from, 
//			'to' => $to,
//			'limit' => 100
//		))->as_object()->get('response');
//
//		$data = array();
//		foreach ($logs as $log)
//		{
//			$data[] = array(
//				'title' => strip_tags($log->message),
//				'start' => strtotime($log->created_on),
//				'allDay' => FALSE,
//				'color' => '#f89406',
//			);
//		}
//		return $data;
//	});
//});