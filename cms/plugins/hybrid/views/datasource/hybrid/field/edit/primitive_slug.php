<div class="widget-content ">
	<div class="control-group">
		<label class="control-label" for="from_header"><?php echo __('Slug from header'); ?></label>
		<div class="controls">
			<?php echo Form::checkbox('from_header', 1, $field->from_header == 1, array('id' => 'from_header' )); ?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="unique"><?php echo __('Unique value'); ?></label>
		<div class="controls">
			<?php echo Form::checkbox('unique', 1, $field->unique == 1, array('id' => 'unique' )); ?>
		</div>
	</div>
	
	<hr />
	
	<div class="control-group">
		<label class="control-label" for="separator"><?php echo __('Slug separator'); ?></label>
		<div class="controls">
			<?php echo Form::input('separator', $field->separator, array(
				'id' => 'separator',
				'maxlength' => 1,
				'class' => 'input-mini'
			) ); ?>
		</div>
	</div>
</div>