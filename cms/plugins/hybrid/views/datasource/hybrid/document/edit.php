<script>
<?php if(!Acl::check('hybrid'.$ds->id().'.document.edit')): ?>
$(function() {
	$('input,textarea,select').attr('disabled', 'disabled');
})
<?php endif; ?>
var API_FORM_ACTION = 'datasource/hybrid-document';
</script>

<div class="outline">
	<div class="widget outline_inner">
	<?php if(Acl::check('hybrid'.$ds->id().'.document.edit')): ?>
	<?php echo Form::open(Request::current()->url() . URL::query(array('id' => $doc->id)), array(
		'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
	)); ?>
	<?php echo Form::hidden('ds_id', $ds->id()); ?>
	<?php echo Form::hidden('id', $doc->id); ?>
	<?php else: ?>
	<div class="form-horizontal">
	<?php endif; ?>
	<div class="widget-title">
		<div class="control-group">
			<label class="control-label title"><?php echo __('Header'); ?></label>
			<div class="controls">
				<?php echo Form::input('header', $doc->header, array(
					'class' => 'input-title input-block-level slug-generator', 'data-slug' => '.from-header'
				)); ?>
			</div>
			
			<div class="controls">
				<?php echo View::factory('datasource/hybrid/document/fields/published', array(
					'doc' => $doc
				)); ?>
			</div>	
		</div>		
	</div>
		
	<?php if($ds->template() !== NULL): ?>
	<?php echo View_Front::factory($ds->template(), array(
		'fields' => $fields,
		'doc' => $doc,
	)); ?>
	<?php elseif(!empty($fields)): ?>
	<br />

	<?php foreach ($fields as $key => $field): ?>
	<?php echo $field->backend_template($doc); ?>
	<?php endforeach; ?>
	<?php endif; ?>

		
	<?php if(Acl::check('hybrid'.$ds->id().'.document.edit')): ?>
	<div class="form-actions widget-footer">
		<?php echo UI::actions(TRUE, Route::url('datasources', array(
			'controller' => 'data',
			'directory' => 'datasources'
		)) . URL::query(array('ds_id' => $ds->id()), FALSE)); ?>
	</div>
	<?php echo Form::close(); ?>
	<?php else: ?>
	</div>
	<?php endif; ?>
</div></div>