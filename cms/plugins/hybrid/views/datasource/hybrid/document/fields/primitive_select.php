<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php echo Form::select( $field->name, $field->get_options(), $value, array(
			'id' => $field->name
		) ); ?>
		
		<?php if($field->custom_option): ?>
		&nbsp
		<?php echo Form::input( $field->name . '_custom', NULL, array(
			'class' => 'input-auto', 'id' => $field->name . '_custom',
			'maxlength' => 50, 'placeholder' => __('Custom value')
		) ); ?>
		<?php endif; ?>
	</div>
</div>