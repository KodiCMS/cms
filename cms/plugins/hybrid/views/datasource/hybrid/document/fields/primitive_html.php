<script>
	jQuery(function(){
		cms.filters.switchOn( '<?php echo $field->name; ?>', '<?php echo $field->filter; ?>', {height: 200});
	});
</script>


<div class="control-group">
	<label class="control-label"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php echo Form::textarea( $field->name, $value, array(
			'class' => 'input-block-level', 'id' => $field->name, 'data-height' => '265'
		) ); ?>
	</div>
</div>