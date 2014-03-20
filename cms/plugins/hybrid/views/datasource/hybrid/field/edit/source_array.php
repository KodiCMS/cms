<div class="widget-content ">
	<div class="control-group">
		<label class="control-label" for="array_type"><?php echo __( 'Datasource' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'type', Datasource_Data_Manager::types(), $field->type, array('disabled')); ?>
		</div>
	</div>
	<?php foreach ( Datasource_Data_Manager::types() as $key => $title ): ?>
		<div class="control-group" id="ds_<?php echo $key; ?>">
			<label class="control-label" for="array_chose_from"><?php echo $title; ?></label>
			<div class="controls">
				<?php echo Form::select( 'from_ds', $sections[$key], $field->from_ds, array('disabled')); ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>