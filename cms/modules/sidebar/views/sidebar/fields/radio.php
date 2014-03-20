<?php if($inline === TRUE): ?>
<label class="radio">
	<?php echo Form::radio($name, $value, $selected, $attributes); ?> <?php if ( !empty($label)): ?><?php echo $label; ?><?php endif; ?>
</label>
<?php else: ?>
<div class="control-group">
	<div class="control-group">
		<div class="controls">
			<label class="radio">
				<?php echo Form::radio($name, $value, $selected, $attributes); ?> <?php if ( !empty($label)): ?><?php echo $label; ?><?php endif; ?>
			</label>
		</div>
	</div>
</div>
<?php endif; ?>
