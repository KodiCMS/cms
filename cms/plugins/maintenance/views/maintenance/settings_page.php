<div class="widget-header spoiler-toggle" data-spoiler=".maintenance-spoiler">
	<h3><?php echo __('Maintenance mode'); ?></h3>
</div>
<div class="widget-content spoiler maintenance-spoiler">
	<div class="control-group">
		<div class="controls">
			<div class="checkbox">
				<label><?php echo Form::checkbox('plugin[maintenance_mode]', 'yes', $plugin->get('maintenance_mode') == 'yes'); ?> <?php echo __('Enable maintenance mode'); ?></label>
			</div>
		</div>
	</div>
</div>