<div class="widget-content">
	<div class="control-group">
		<label class="control-label" for="field_id"><?php echo __('Hybrid field'); ?></label>
		<div class="controls">
			<?php echo Form::select( 'field_id', $widget->get_doc_fields(), $widget->field_id, array(
				'class' => 'input-large', 'id' => 'field_id'
			) ); ?>
		</div>
	</div>
</div>