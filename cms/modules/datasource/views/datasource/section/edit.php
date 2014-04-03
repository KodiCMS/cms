<script>
	var DS_ID = '<?php echo $ds->id(); ?>';
</script>

<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<?php echo Form::hidden('ds_id', $ds->id()); ?>

	<div class="widget-header">
		<h4><?php echo __('Datasource Information'); ?></h4>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label" for="name"><?php echo __('Datasource Header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'name', $ds->name, array(
					'class' => 'input-xlarge', 'id' => 'name'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="description"><?php echo __('Datasource Description'); ?></label>
			<div class="controls">
				<?php
				echo Form::textarea( 'description', $ds->description, array(
					'class' => 'input-xlarge', 'id' => 'description'
				) );
				?>
			</div>
		</div>
	</div>
	
	<div class="form-actions widget-footer">
		<?php echo UI::actions(NULL, Route::get('datasources')->uri(array(
			'controller' => 'data',
			'directory' => 'datasources'
		))); ?>
	</div>
<?php echo Form::close(); ?>
</div>