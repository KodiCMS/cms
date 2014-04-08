<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Observer::observe('part_before_save', function($part) {
	$data = array(
		'part_id' => $part->id, 
		'created_on' => date('Y-m-d H:i:s'),
		'content' => $part->content
	);
	DB::insert('part_revision')
		->columns(array_keys($data))
		->values($data)
		->execute();
});

Observer::observe('part_option', function() {
	$url = Route::get('backend')->uri(array(
		'controller' => 'part',
		'action' => 'revision',
		'id' => '<%=id%>'
	));
	
	echo '<a class="btn" href="'.$url.'">'.__('Part revision').'</a>';
});