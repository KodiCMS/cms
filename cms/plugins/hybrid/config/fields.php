<?php // defined('SYSPATH') or die('No direct access allowed.');

return array(
	'Primitive' => array(
		'primitive_boolean' => 'Boolean',
	
		// Text
		'primitive_string' => 'String',
		'primitive_text' => 'Text',
		'primitive_html' => 'HTML',

		// Numeric
		'primitive_integer' => 'Integer',
		'primitive_float' => 'Float',

		'primitive_slug' => 'Slug',
		'primitive_email' => 'Email',
		'primitive_select' => 'Select',

		// Dates
		'primitive_date' => 'Date',
		'primitive_time' => 'Time',
		'primitive_datetime' => 'Datetime',
	),
	'Source' => array(
		'source_document' => 'Document',
		'source_array' => 'Array of documents',
		'source_tags' => 'Tags',
		'source_user' => 'User',
		'source_free' => 'Free',
	),
	
	'File' => array(
		'file_file' => 'File',
		'file_image' => 'Image',
	)
);