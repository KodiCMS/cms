<fieldset id="f-<?php echo DataSource_Hybrid_Field::TYPE_DATASOURCE; ?>" disabled="disabled">
	<hr />
	<div class="control-group">
		<label class="control-label" for="ds_type"><?php echo __( 'Datasource' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'type', Datasource_Manager::types(), Arr::get($post_data, 'type')); ?>
		</div>
	</div>
	<?php foreach ( Datasource_Manager::types() as $key => $title ): ?>
		<div class="control-group" id="ds_<?php echo $key; ?>">
			<label class="control-label" for="ds_chose_from"><?php echo $title; ?></label>
			<div class="controls">
				<?php echo Form::select( 'from_ds', $sections[$key], Arr::get($post_data, 'from_ds')); ?>
			</div>
		</div>
	<?php endforeach; ?>
</fieldset>