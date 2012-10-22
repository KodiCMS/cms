<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->header; ?></label>
	<div class="controls">
		<?php echo Form::input( $field->name, $value, array(
			'class' => 'input-auto input-date', 'id' => $field->name,
			'maxlength' => $field->length, 'size' => 10
		) ); ?>
	</div>
</div>