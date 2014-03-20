<div class="widget-content ">
	<div class="control-group">
		<label class="control-label" for="length"><?php echo __('Field options'); ?></label>
		<div class="controls">
			<?php echo Form::textarea('options', implode("\n", $field->options)); ?>
			
			<div class="help-block"><?php echo __('Set each new value appear in a new line'); ?></div>
		</div>
	</div>
	
	<hr />
	<div class="control-group">
		<div class="controls">
			<label class="checkbox"><?php echo Form::checkbox('custom_option', 1, $field->custom_option == 1, array('id' => 'custom_option' )); ?> <?php echo __('Can use custom value'); ?></label>
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<label class="checkbox"><?php echo Form::checkbox('empty_value', 1, $field->empty_value == 1, array('id' => 'empty_value' )); ?> <?php echo __('Can select empty value'); ?></label>
		</div>
	</div>
</div>