<script>
	var DS_ID = '<?php echo $ds->ds_id; ?>';
</script>

<?php echo UI::page_header(__( 'Edit datasource hybrid' )); ?>

<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<div class="row-fluid">
		<div class="span6 well">
			<?php echo Form::hidden('ds_id', $ds->ds_id); ?>
			<fieldset>
				<legend><?php echo __('Datasource Information'); ?></legend>

				<div class="control-group">
					<label class="control-label" for="ds_key"><?php echo __('Datasource Key'); ?></label>
					<div class="controls">
						<?php echo UI::field($ds->key); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="ds_name"><?php echo __('Datasource Header'); ?></label>
					<div class="controls">
						<?php
						echo Form::input( 'ds_name', $ds->name, array(
							'class' => 'input-xlarge', 'id' => 'ds_name'
						) );
						?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="ds_description"><?php echo __('Datasource Description'); ?></label>
					<div class="controls">
						<?php
						echo Form::textarea( 'ds_description', $ds->description, array(
							'class' => 'input-xlarge', 'id' => 'ds_description'
						) );
						?>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="span6 well">
			<?php echo View::factory('datasource/data/hybrid/blocks/fields', array(
				'record' => $record, 'ds' => $ds
			)); ?>
		</div>
	</div>
	<div class="form-actions">
		<?php echo UI::actions('datasources/data'); ?>
	</div>

<?php echo Form::close(); ?>