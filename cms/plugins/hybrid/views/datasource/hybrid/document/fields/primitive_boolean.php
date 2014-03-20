<div class="control-group">
	<label class="control-label"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<label class="radio inline">
			<?php echo Form::radio($field->name, 1, $value == 1); ?> <?php echo __('Yes'); ?>
		</label>

		<label class="radio inline">
			<?php echo Form::radio($field->name, 0, $value == 0); ?> <?php echo __('No'); ?>
		</label>
	</div>
</div>