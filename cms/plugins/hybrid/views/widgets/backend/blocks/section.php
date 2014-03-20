<div class="widget-content">
	<div class="control-group">
		<label class="control-label" for="ds_id"><?php echo __('Hybrid section'); ?></label>
		<div class="controls">
			<?php echo Form::select( 'ds_id', $widget->options(), $widget->ds_id, array(
				'class' => 'input-large', 'id' => 'ds_id'
			) ); ?>
		</div>
	</div>
</div>