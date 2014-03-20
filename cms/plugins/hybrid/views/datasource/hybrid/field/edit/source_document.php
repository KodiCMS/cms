<div class="widget-content ">
	<div class="control-group">
		<label class="control-label" for="document_type"><?php echo __( 'Datasource' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'type', Datasource_Data_Manager::types(), $field->type, array('disabled')); ?>
		</div>
	</div>
	<?php foreach ( Datasource_Data_Manager::types() as $key => $title ): ?>
	<div class="control-group" id="ds_<?php echo $key; ?>">
		<label class="control-label" for="from_ds"><?php echo $title; ?></label>
		<div class="controls">
			<?php echo Form::select( 'from_ds', $sections[$key], $field->from_ds, array('disabled')); ?>
		</div>
	</div>
	<?php endforeach; ?>
	
	<hr />
	
	<div class="control-group">
		<label class="control-label" for="one_to_one"><?php echo __('One to one relation'); ?></label>
		<div class="controls">
			<div class="checkbox">
				<?php echo Form::checkbox( 'one_to_one', 1, ($field->one_to_one == 1), array(
					'id' => 'one_to_one'
				)); ?>
			</div>
		</div>
	</div>
</div>