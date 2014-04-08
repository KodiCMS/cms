<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'vk' => array(
		'name' => __('Vkontakte'),
		'create_link' => 'http://vk.com/editapp?act=create',
		'account_link' => 'http://vk.com/:id'
	),
	'twitter' => array(
		'name' => 'Twitter',
		'create_link' => 'https://dev.twitter.com/apps/new',
		'account_link' => 'https://twitter.com/:id'
	),
	'facebook' => array(
		'name' => 'Facebook',
		'create_link' => 'https://developers.facebook.com/apps?ref=mb',
		'account_link' => 'http://facebook.com/:id'
	),
	'yandex' => array(
		'name' => __('Yandex'),
		'create_link' => 'https://oauth.yandex.ru/client/new',
		'account_link' => 'https://passport.yandex.ru/passport?mode=passport'
	),
	'github' => array(
		'name' => 'Github',
		'create_link' => 'https://github.com/settings/applications/new',
		'account_link' => 'https://github.com/:id'
	),
	'google' => array(
		'name' => 'Google',
		'create_link' => 'https://accounts.google.com/ManageDomains',
		'account_link' => 'https://plus.google.com/:id'
	),
	'disqus' => array(
		'name' => 'Disqus',
		'create_link' => 'http://disqus.com/api/applications/register/',
		'account_link' => 'http://disqus.com/ButscH/:username'
	)
);
