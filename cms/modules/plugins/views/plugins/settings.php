<div id="settings">
	<?php echo Form::open(NULL, array('class' => Bootstrap_Form::HORIZONTAL)); ?>
	
	<?php echo Form::hidden('token', Security::token()); ?>
	<div class="widget">
		
		<?php echo $content; ?>
	
		<div class="form-actions widget-footer">
			<?php echo UI::actions('plugins'); ?>
		</div>
	</div>
	<?php echo Form::close(); ?>
</div>