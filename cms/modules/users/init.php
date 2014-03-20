<?php defined('SYSPATH') or die('No direct access allowed.');

/*
* Set current lang
*/
Observer::observe('modules::afer_load', function() {
	I18n::lang( Model_User::locale() );
	I18n::available_langs();
});