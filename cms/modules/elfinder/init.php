<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Observer::observe('modules::afer_load', function() {
	$i18n_lang = substr(I18n::lang(), 0, 2);
	Assets_Package::add('elfinder')
		->js('elfinder.lib', ADMIN_RESOURCES . 'libs/elfinder/js/elfinder.min.js', 'global')
		->js('elfinder.'.$i18n_lang, ADMIN_RESOURCES . 'libs/elfinder/js/i18n/elfinder.'.$i18n_lang.'.js', 'elfinder.lib')
		->css('elfinder.lib', ADMIN_RESOURCES . 'libs/elfinder/css/elfinder.min.css')
		->css('elfinder', ADMIN_RESOURCES . 'css/elfinder.css', 'elfinder.lib');
});
