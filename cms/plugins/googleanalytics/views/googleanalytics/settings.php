<div class="widget-header">
	<h3><?php echo __('General settings'); ?></h3>
</div>
<div class="widget-content">
	<div class="control-group">
		<?php echo Form::label('setting_counter_id', __('Counter ID'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[counter_id]', $plugin->get('counter_id', 00000000), array(
				'id' => 'setting_counter_id', 'class' => '', 'maxlength' => 20, 'size' => 20
			)); ?>
		</div>
	</div>
</div>