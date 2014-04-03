<script>
	var DS_ID = '<?php echo $ds->id(); ?>';
</script>
<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<div class="widget-header">
		<h3><?php echo __( 'Field description' ); ?></h3>
	</div>
	<div class="widget-content" id="filed-type">
		<div class="control-group">
			<label class="control-label" for="header"><?php echo __('Field header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'header', Arr::get($post_data, 'header', $field->header), array(
					'class' => 'input-xlarge', 'id' => 'header'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name"><?php echo __('Field key'); ?></label>
			<div class="controls">
				<?php echo Form::hidden( 'name', Arr::get($post_data, 'name', $field->name)); ?>
				<?php echo Form::hidden( 'in_headline', Arr::get($post_data, 'in_headline', $field->in_headline)); ?>
				<span class="input-xlarge uneditable-input"><?php echo $field->name; ?></span>
			</div>
		</div>
	</div>
	
	<div class="widget-header">
		<h3><?php echo __( 'Field settings' ); ?></h3>
	</div>
	<div class="widget-content ">
		<?php
		try
		{
			if( ! empty($post_data))
			{
				$field->set($post_data);
			}
			echo View::factory('datasource/hybrid/field/edit/' . $type, array(
				'field' => $field, 'sections' => $sections
			));
		}
		catch(Exception $e) {} 
		?>
		<hr />
		<div class="control-group">
			<label class="control-label" for="hint"><?php echo __('Field hint'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'hint', $field->hint, array(
					'id' => 'hint',
					'class' => 'input-xxlarge'
				)); ?>
			</div>
		</div>
		
		<?php if($field->is_required()): ?>
		<div class="control-group">
			<label class="control-label" for="isreq"><?php echo __('Required'); ?></label>
			<div class="controls">
				<div class="checkbox">
					<?php echo Form::checkbox( 'isreq', 1, (Arr::get($post_data, 'isreq', $field->isreq) == 1), array(
						'id' => 'isreq'
					)); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="control-group">
			<label class="control-label" for="position"><?php echo __('Field position'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'position', $field->position, array(
					'id' => 'position',
					'class' => 'input-mini'
				)); ?>
			</div>
		</div>
	</div>
	<div class="widget-footer form-actions">
		<?php echo UI::actions(NULL, Route::get('datasources')->uri(array(
			'controller' => 'section',
			'directory' => 'datasources',
			'action' => 'edit',
			'id' => $ds->id()
		))); ?>
	</div>
<?php echo Form::close(); ?>
</div>