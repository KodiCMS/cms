<div class="widget-content ">
	<div class="control-group">
		<label class="control-label" for="primitive_default"><?php echo __( 'Default value' ); ?></label>
		<div class="controls">
			<?php
			echo Form::input( 'default', Arr::get($post_data, 'default', $field->default), array(
				'class' => 'input-xlarge', 'id' => 'primitive_default'
			) );
			?>
		</div>
	</div>

	<hr />

	<div class="control-group">
		<label class="control-label" for="length"><?php echo __('Field length'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'length', Arr::get($post_data, 'length', $field->length), array(
				'class' => 'input-xlarge', 'id' => 'length'
			) ); ?>
		</div>
	</div>
	
	<hr />

	<div class="control-group">
		<div class="controls">
			<?php echo __('Min'); ?> <?php echo Form::input( 'min', Arr::get($post_data, 'min', $field->min), array(
				'class' => 'input-mini', 'id' => 'min'
			) ); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo __('Max'); ?> <?php echo Form::input( 'max', Arr::get($post_data, 'max', $field->max), array(
				'class' => 'input-mini', 'id' => 'max'
			) ); ?>
		</div>
	</div>
</div>