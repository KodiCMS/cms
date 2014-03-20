<div class="widget">
	<?php echo Form::open(Request::current()->uri(), array(
		'class' => Bootstrap_Form::HORIZONTAL
	)); ?>
	<div class="widget-content">
		<?php if(!empty($patches)): ?>
		<div class="control-group">
			<label class="control-label"><?php echo __('Patch list'); ?></label>
			<div class="controls">
				<?php echo Form::select( 'patch', $patches, NULL, array(
					'class' => 'input-medium'
				) ); ?>
			</div>
		</div>
		<?php else: ?>
		<h2><?php echo __('No available patches'); ?></h2>
		<?php endif; ?>
	</div>
	
	<div class="widget-footer form-actions">
		<div class="control-group">
			<div class="controls">
				<?php echo Form::button('apply', __('Apply'), array('class' => 'btn btn-large btn-confirm btn-danger')); ?>
			</div>
		</div>
		
	</div>
	<?php echo Form::close(); ?>
</div>