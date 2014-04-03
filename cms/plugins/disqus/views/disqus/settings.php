<div class="widget-header">
	<h3><?php echo __('General settings'); ?></h3>
</div>
<div class="widget-content">
	<div class="control-group">
		<?php echo Form::label('setting_disqus_id', __('Profile ID'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[disqus_id]', $plugin->get('disqus_id'), array(
				'id' => 'setting_disqus_id', 'class' => ''
			)); ?>
		</div>
	</div>
</div>