<?php echo Form::open(Route::url('backend', array(
	'controller' => 'layout', 
	'action' => $action, 
	'id' => $layout->name)), array(
		'id' => 'layoutEditForm', 
		'class' => 
		'form-horizontal')); ?>

	<?php echo Form::hidden('token', Security::token()); ?>
	<?php echo Form::hidden('layout_name', $layout->name); ?>

	<div class="widget widget-nopad">
		<div class="widget-title">
			<div class="control-group">
				<label class="control-label title" for="layoutEditNameField"><?php echo __('Layout name'); ?></label>
				<div class="controls">
					<div class="row-fluid">
					<?php echo Form::input('name', $layout->name, array(
						'class' => 'slug focus span12 input-title', 'id' => 'layoutEditNameField',
						'tabindex'	=> 1
					)); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="widget-header widget-inverse">
			<h4><?php echo __('Content'); ?></h4>
			
			<?php if( $layout->is_writable() OR ! $layout->is_exists()): ?>
			<?php echo UI::button(__('File manager'), array(
				'class' => 'btn btn-filemanager', 'data-el' => 'textarea_content',
				'icon' => UI::icon( 'folder-open')
			)); ?>
			<?php endif; ?>
		</div>
		<div class="widget-content">
			<?php echo Form::textarea('content', $layout->content, array(
				'tabindex'		=> 2,
				'id'			=> 'textarea_content',
				'data-readonly'		=> ( ! $layout->is_exists() OR ($layout->is_exists() AND $layout->is_writable())) ? 'off' : 'on'
			)); ?>
		</div>
		<?php if(
			(ACL::check('layout.edit')
		AND
			(
				! $layout->is_exists() 
			OR 
				($layout->is_exists() AND $layout->is_writable())
			))
		OR ! ACL::check( 'layout.view') ): ?>
		<div class="form-actions widget-footer">
			<?php echo UI::actions($page_name); ?>
		</div>
		<?php endif; ?>
	</div>
<?php echo Form::close(); ?>