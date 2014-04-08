<?php echo UI::button(__('Send message'), array(
	'class' => 'btn btn-warning popup fancybox.iframe',
	'href' => Route::get('backend')->uri(array('controller' => 'messages', 'action' => 'add')) . URL::query(array('to' => $user_id))
)); ?>