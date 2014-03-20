<div class="control-group">
	<?php if (!empty($label)): ?>
    <?php echo Form::label('', $label, array('class' => 'control-label')); ?>
	<?php endif; ?>
    <div class="controls">
		<?php foreach ($options as $option): ?>
			<?php echo $option; ?>
		<?php endforeach; ?>
    </div>
</div>