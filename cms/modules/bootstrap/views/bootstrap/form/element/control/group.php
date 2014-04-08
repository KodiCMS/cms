<div<?php echo $attributes; ?>>
<?php if( $element->get('element') instanceof Bootstrap_Helper_Element ): ?>
	<?php if($element->get('element') instanceof Bootstrap_Form_Element_Checkbox): ?>
	<div class="controls">
		<?php echo $element->get('element'); ?>
	</div>
	<?php else: ?>
	<?php if($element->get('element')->get('label')): ?>
	<?php echo $element
			->get('element')
			->get('label')
			->attributes('class', 'control-label'); ?>
	<?php endif; ?>
	<div class="controls">
		<?php echo $element->get('element')->content(); ?>
		
		<?php if( isset($element->get('element')->help_text)) echo $element->get('element')->help_text; ?>
	</div>
	
	<?php endif; ?>
<?php else: ?>
	<?php if($element->get('label')): ?>
	<?php echo $element
			->get('label')
			->attributes('class', 'control-label'); ?>
	<?php endif; ?>
	<div class="controls">
		<?php echo $content; ?>
	</div>
<?php endif; ?>
</div>