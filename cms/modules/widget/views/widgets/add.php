<div class="widget">

<?php echo Form::open(Request::current()->uri(), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>	
	<div class="widget-header">
		<h4><?php echo __('General Information'); ?></h4>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label"><?php echo __('Widget Header'); ?></label>
			<div class="controls">
				<?php
				echo Form::input( 'name', NULL, array(
					'class' => Bootstrap_Form_Element_Input::BLOCK_LEVEL
				) );
				?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo __('Widget Description'); ?></label>
			<div class="controls">
				<?php
				echo Form::textarea( 'description', NULL, array(
					'class' => 'input-xlarge'
				) );
				?>
			</div>
		</div>
	</div>
	<div class="widget-header">
		<h4><?php echo __('Widget data'); ?></h4>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label"><?php echo __('Type'); ?></label>
			<div class="controls">
				<div class="row-fluid">
					<?php echo Form::select( 'type', $types, NULL, array(
						'class' => 'span6'
					) ); ?>
				</div>
				
			</div>
		</div>
	</div>
	<div class="widget-footer form-actions">
		<?php echo UI::button( __('Create widget'), array(
			'icon' => UI::icon( 'plus'), 'class' => 'btn btn-large'
		)); ?>
	</div>
<?php echo Form::close(); ?>
</div>