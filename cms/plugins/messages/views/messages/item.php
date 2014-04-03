<div id="message_<?php echo $message->id; ?>" class="message widget-content  <?php if(AuthUser::getId() == $message->from_user_id): ?>own-message<?php endif; ?> <?php if( $message->is_read == Model_API_Message::STATUS_NEW AND AuthUser::getId() != $message->from_user_id ): ?>new-message<?php endif; ?>">
	<h4><?php echo HTML::anchor(Route::get('backend')->uri(array(
			'controller' => 'users',
			'action' => 'profile',
			'id' => $message->from_user_id
		)), $message->author); ?> <small><?php echo Date::format($message->created_on, 'j F Y H:i:s'); ?></small></h4> 
	<div class="message-text"><?php echo $message->text; ?></div>
</div>