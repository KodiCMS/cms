<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<div class="widget-header">
		<h4><?php echo __('General Information'); ?></h4>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label title" for="name"><?php echo __('Datasource Header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'name', Arr::get($data, 'name'), array(
					'class' => 'input-xlarge input-title span12', 'id' => 'name'
				) ); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="description"><?php echo __('Datasource Description'); ?></label>
			<div class="controls">
				<?php echo Form::textarea( 'description', Arr::get($data, 'description'), array(
					'class' => 'input-xlarge', 'id' => 'description', 'rows' => 3
				) ); ?>
			</div>
		</div>
	</div>
	
	<div class="widget-footer form-actions">
		<?php echo UI::button( __('Create section'), array(
			'icon' => UI::icon( 'plus'), 'class' => 'btn btn-large'
		)); ?>
	</div>
<?php echo Form::close(); ?>
</div>