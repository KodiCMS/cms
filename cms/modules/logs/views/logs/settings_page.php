<div class="widget-header spoiler-toggle" data-spoiler=".logs-spoiler" data-icon="time">
	<h3><?php echo __('Log settings'); ?></h3>
</div>
<div class="widget-content spoiler logs-spoiler">
	<div class="well">
		<?php echo UI::button(__('Clear logs older 30 days'), array(
			'icon' => UI::icon( 'trash' ),
			'id' => 'clear-logs',
			'class' => 'btn btn-warning'
		)); ?>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo __( 'Logs level' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'setting[site][log_level]', Log::levels(), (int) Config::get('site', 'log_level' )); ?>
		</div>
	</div>
</div>