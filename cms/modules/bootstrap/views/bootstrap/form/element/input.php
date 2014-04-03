<?php if( isset($element->label) ): ?>
<?php echo $element->label; ?>
<?php endif; ?>

<?php echo $content; ?>

<?php if( isset($element->help_text)) echo $element->help_text; ?>