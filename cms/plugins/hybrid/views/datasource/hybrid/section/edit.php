<script>
	var DS_ID = '<?php echo $ds->id(); ?>';
</script>

<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<?php echo Form::hidden('ds_id', $ds->id()); ?>

	<div class="widget-header spoiler-toggle" data-spoiler=".general-spoiler">
		<h4><?php echo __('Datasource Information'); ?></h4>
	</div>
	<div class="widget-content spoiler general-spoiler">
		<div class="control-group">
			<label class="control-label title" for="ds_name"><?php echo __('Datasource Header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'name', $ds->name, array(
					'class' => 'input-title input-block-level', 'id' => 'ds_name'
				) ); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ds_description"><?php echo __('Datasource Description'); ?></label>
			<div class="controls">
				<?php echo Form::textarea( 'description', $ds->description, array(
					'class' => 'input-block-level', 'id' => 'ds_description', 'rows' => 4
				) ); ?>
			</div>
		</div>
		
	</div>
	
	<?php echo View::factory('datasource/hybrid/blocks/fields', array(
		'record' => $ds->record(), 'ds' => $ds
	)); ?>
	
	<?php echo View::factory('helper/snippet_select', array(
		'header' => __('Document template'),
		'template' => $ds->template,
	)); ?>

	<div class="widget-header spoiler-toggle" data-spoiler=".indexer-spoiler">
		<h4><?php echo __('Search indexation'); ?></h4>
	</div>
	<div class="widget-content spoiler indexer-spoiler">
		<div class="control-group">
			<div class="controls">
				<label class="checkbox"><?php echo Form::checkbox( 'is_indexable', 1, $ds->is_indexable() ); ?> <?php echo __('Is indexable'); ?></label>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="search_intro_field"><?php echo __('Index document intro'); ?></label>
			<div class="controls">
				<?php echo Form::select('search_intro_field',  array(__('--- none ---')) + $ds->record_fields_array(), $ds->search_intro_field); ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="search_index_fields"><?php echo __('Index document fields'); ?></label>
			<div class="controls">
				<?php echo Form::select('search_index_fields[]', $ds->record_fields_array(), (array) $ds->search_index_fields, array(
					'class' => 'input-block-level'
				)); ?>
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