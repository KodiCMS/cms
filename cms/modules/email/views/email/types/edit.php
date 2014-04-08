<script type="text/javascript">
	var EMAIL_TYPE_ID = <?php echo (int) $type->id; ?>;
</script>

<?php echo Form::open(Route::get('email_controllers')->uri(array('controller' => 'types', 'action' => $action, 'id' => $type->id)), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>

<?php echo Form::hidden('token', Security::token()); ?>
<div class="widget">
	<div class="widget-header">
		<h3><?php echo __('General information'); ?></h3>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label title" for="name"><?php echo __( 'Email type name' ); ?></label>
			<div class="controls">
				<?php
				echo Form::input( 'name', $type->name, array(
					'class' => 'input-title input-block-level', 'id' => 'subject'
				) );
				?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="code"><?php echo __( 'Email type code' ); ?></label>
			<div class="controls">
				<?php if($action == 'add'): ?>
				<?php echo Form::input( 'code', $type->code, array(
					'class' => 'slug', 'id' => 'code', 'data-separator' => '_'
				) ); ?>
				<?php else: ?>
				<span class="input-xlarge uneditable-input"><?php echo $type->code; ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<div class="widget-header">
		<h3><?php echo __('Email type fileds'); ?></h3>
	</div>
	<div class="widget-content" id="type-fields">
		<?php echo View::factory('helper/rows', array(
			'field' => 'data',
			'data' => $type->data()
		)); ?>
	</div>
	
	<?php if($action == 'edit'): ?>
	<div class="widget-header">
		<h3><?php echo __('Linked email templates'); ?></h3>
	</div>
	<div class="widget-content">
		<?php if(count($templates) > 0): ?>
		<ul class="unstyled">
		<?php foreach($templates as $tpl): ?>
			<li><?php echo HTML::anchor(Route::get('email_controllers')->uri(array(
				'controller' => 'templates',
				'action' => 'edit',
				'id' => $tpl->id
			)), $tpl->subject); ?></li>
		<?php endforeach; ?>
		</ul>
		<hr />
		<?php endif; ?>
		
		<?php if ( Acl::check( 'email_template.add')): ?>
		<?php echo UI::button(__('Add linked template'), array(
			'href' => Route::get( 'email_controllers')->uri(array('controller' => 'templates', 'action' => 'add')) . '?email_type='.$type->id, 'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<div class="form-actions widget-footer">
		<?php echo UI::actions($page_name); ?>
	</div>
</div>
<?php Form::close(); ?>