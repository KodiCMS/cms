<?php if($inline === TRUE): ?>
	<?php echo Form::input($name, $value, $attributes); ?>
<?php else: ?>
<div class="control-group">
	<?php if (!empty($label)): ?>
    <?php echo Form::label($attributes['id'], $label, array('class' => 'control-label')); ?>
	<?php endif; ?>
    <div class="controls">
		<?php echo Form::input($name, $value, $attributes); ?>
    </div>
</div>
<?php endif; ?>
