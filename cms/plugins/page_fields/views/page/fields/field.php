<div class="page-field row-fluid" data-id="<?php echo $field->id; ?>">
	<div class="span3 system-field">
		<?php if($field->loaded()): ?>
		<?php echo FORM::input('title', $field->title, array(
			'placeholder' => __('Field title'), 'data-slug' => '.field-slug',
			'class' => 'input-block-level', 'disabled'
		)); ?>
		<?php else: ?>

		<?php echo FORM::input('title', $field->title, array(
			'placeholder' => __('Field title'), 'data-slug' => '.field-slug',
			'class' => 'input-block-level'
		)); ?>
		<?php endif; ?>
	</div>
	
	<div class="span2 system-field">
		<?php if($field->loaded()): ?>
		<?php echo FORM::input('key', $field->key, array(
			'placeholder' => __('Field key'), 'disabled',
			'class' => 'input-block-level slug field-slug', 'data-separator' => '_'
		)); ?>
		<?php else: ?>
		<?php echo FORM::input('key', $field->key, array(
			'placeholder' => __('Field key'), 
			'class' => 'input-block-level slug field-slug', 'data-separator' => '_'
		)); ?>
		<?php endif; ?>
	</div>

	<div class="span6 input-append">
		<?php echo FORM::input('value', $field->value, array(
			'placeholder' => (empty($field->value) AND $field->loaded()) ? '' : __('Field value'), 
			'class' => 'input-block-level input-filemanager'
		)); ?>

		<?php if($field->loaded()): ?>
		<?php echo FORM::button('remove_field', UI::icon( 'trash'), array(
			'class' => 'btn btn-danger btn-remove'
		)); ?>
		<?php else: ?>
		<?php echo FORM::button('add_field', UI::icon( 'plus'), array(
			'class' => 'btn btn-success btn-add'
		)); ?>
		<?php endif; ?>
	</div>
	
	<?php if($field->loaded()): ?>
	<div class="clearfix"></div>
	<hr />
	<?php endif; ?>
</div>

	