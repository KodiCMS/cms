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
		<label class="control-label" for="regexp"><?php echo __('Field validation'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'regexp', Arr::get($post_data, 'regexp', $field->regexp), array(
				'class' => 'input-xlarge', 'id' => 'regexp'
			) ); ?>
			
			<span class="flags">
				<span class="label" data-value="url"><?php echo __('URL'); ?></span>
				<span class="label" data-value="phone"><?php echo __('Phone number'); ?></span>
				<span class="label" data-value="email"><?php echo __('Email'); ?></span>
				<span class="label" data-value="email_domain"><?php echo __('Email domain'); ?></span>
				<span class="label" data-value="ip"><?php echo __('IP'); ?></span>
				<span class="label" data-value="credit_card"><?php echo __('Credit card'); ?></span>
				<span class="label" data-value="date"><?php echo __('Date'); ?></span>
				<span class="label" data-value="alpha"><?php echo __('Alpha'); ?></span>
				<span class="label" data-value="alpha_dash"><?php echo __('Alpha and hyphens'); ?></span>
				<span class="label" data-value="alpha_numeric"><?php echo __('Alpha and numbers'); ?></span>
				<span class="label" data-value="digit"><?php echo __('Integer digit'); ?></span>
				<span class="label" data-value="decimal"><?php echo __('Decimal'); ?></span>
				<span class="label" data-value="numeric"><?php echo __('Numeric'); ?></span>
				<span class="label" data-value="color"><?php echo __('Color'); ?></span>
			</span>
			
			<div class="help-block"><?php echo __('Regular expression or Valid class method :link', array(
				':link' => HTML::anchor( 'http://kohanaframework.org/3.3/guide/kohana/security/validation#provided-rules', NULL, array(
					'target' => 'blank'
				))
			)); ?></div>
		</div>
	</div>
</div>