<?php
echo Form::open($element->get('action'), $attributes->as_array());
echo $content;
?>

<?php if(count($buttons) > 0): ?>
<div class="form-actions">
	<?php echo $buttons; ?>
</div>
<?php endif; ?>

<?php echo Form::close(); ?>