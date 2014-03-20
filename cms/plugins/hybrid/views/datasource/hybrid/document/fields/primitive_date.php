<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php echo Form::input( $field->name, $value, array(
			'class' => 'input-auto datepicker', 'id' => $field->name,
			'size' => 10
		) ); ?>
	</div>
</div>