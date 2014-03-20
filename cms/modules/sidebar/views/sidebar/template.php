<?php echo $form; ?>
<div class="widget widget-sidebar">
	<div class="widget-content">
		<?php foreach ($fields as $field): ?>
		<?php echo $field; ?>
		<?php endforeach; ?>
		<div class="form-actions" style="margin-bottom: 0; padding: 10px 10px;">
			<button type="submit" class="btn btn-primary"><?php echo UI::icon('search'); ?> <?php echo __('Search'); ?></button>
			<?php echo HTML::anchor($form->action(), __('Cancel'), array('class' => 'btn btn-link')); ?>
		</div>
	</div>
	
</div> 
<?php echo Form::close(); ?>
