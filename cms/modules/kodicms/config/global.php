<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'default_locale' => I18n::detect_lang(),
	'date_formats' => array(
		'Y-m-d',
		'd.m.Y',
		'Y/m/d',
		'm/d/Y',
		'd/m/Y',
		'd M.',
		'd M. Y',
		'd F',
		'd F Y',
		'd F Y H:i',
		'l, j-S F Y'
	),
	
	'allowed_html_tags' => array(
		'b' => array(), 'strong' => array(),
		'i' => array(), 'em' => array(),
		'ol' => array('class' => 1), 'ul' => array('class' => 1), 'li' => array(),
		'p' => array('align' => 1, 'class' => 1),
		'br' => array(), 'hr' => array(),
		'h2' => array(), 'h3' => array(), 'h4' => array(),
		'a' => array('href' => 1, 'class' => 1),
		'blockquote' => array('class' => 1),
		'img' => array('src' => 1, 'class' => 1, 'style' => 1),
		'iframe' => array('width' => 1, 'height' => 1, 'src' => 1, 'allowfullscreen' => 1)
	),
);
