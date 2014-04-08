<?php 
	if(empty($doc->id)) $value = AuthUser::getId();
?>

<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php if($field->only_current): ?>
		<?php echo Form::hidden( $field->name, $value); ?>
		<?php echo Form::select( '', $field->get_users(), $value, array('disabled')); ?>
		<?php else: ?>
		<?php echo Form::select( $field->name, $field->get_users(), $value); ?>
		<?php endif; ?>
		
		<?php if($field->is_exists($value)): ?>
		&nbsp;
		<?php echo HTML::anchor(
				Route::get('backend')->uri(array(
					'controller' => 'users', 
					'action' => 'edit', 
					'id' => $value)
				), 
				__('Show profile'), 
				array('class' => 'popup fancybox.iframe btn')
		); ?>
		<?php endif; ?>
		
		<?php if(isset($field->hint)): ?>
		<span class="help-block"><?php echo $field->hint; ?></span>
		<?php endif; ?>
	</div>
</div>