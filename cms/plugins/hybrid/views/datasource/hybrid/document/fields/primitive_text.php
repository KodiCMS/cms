<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php
		echo Form::textarea( $field->name, $value, array(
			'class' => 'input-xxlarge', 
			'id' => $field->name,
			'rows' => $field->rows,
		) );
		?>
	</div>
</div>