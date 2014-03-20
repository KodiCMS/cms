<div class="control-group">
	<?php if (!empty($label)): ?>
	<?php echo Form::label($attributes['id'], $label, array('class' => 'control-label')); ?>
	<?php endif; ?>
    <div class="controls">
		<?php echo Form::file($name, $attributes); ?>
    </div>
</div>