<?php defined('SYSPATH') or die('No direct access allowed.');

Observer::observe('frontpage_found', function($page) {
	if($page->is_password_protected() AND ! AuthUser::isLoggedIn())
	{
		throw new HTTP_Exception_Front_401;
	}
	
	Meta::clear();
	Context::instance()->meta(Meta::factory($page));
});

Observer::observe('scheduler_callbacks', function() {
	scheduler::add(function($from, $to) {
		$from = date('Y-m-d', $from);
		$to = date('Y-m-d', $to);

		$pages = ORM::factory('page')
				->where(DB::expr('DATE(published_on)'), 'between', array($from, $to))
				->find_all();

		$data = array();
		foreach ($pages as $page)
		{
			$data[] = array(
				'title' => $page->title,
				'start' => strtotime($page->published_on),
				'url' => URL::backend('page/edit/' . $page->id),
				'allDay' => FALSE
			);
		}
		return $data;
	});
});