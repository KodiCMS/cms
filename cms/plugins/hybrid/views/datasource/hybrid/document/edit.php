<script>
<?php if(!Acl::check('hybrid'.$ds->id().'.document.edit')): ?>
$(function() {
	$('input,textarea,select').attr('disabled', 'disabled');
})
<?php endif; ?>
var API_FORM_ACTION = '/datasource/hybrid-document.<?php if($doc->loaded()): ?>update<?php else: ?>create<?php endif; ?>'; 

$(function() {
	$('body').on('post:api:datasource:hybrid-document.create ', update_documents);
	$('body').on('put:api:datasource:hybrid-document.create ', update_documents);
});

function update_documents(e, response) {
	var target_field = cms.popup_target.data('target');
	if( target_field && response.id) {
		var current_val = $('#'+target_field).select2("val");
		if(_.isArray(current_val))
			current_val.push(response.id);
		else
			current_val = response.id;

		$('#'+target_field).select2("val", current_val)
	}
}
</script>

<div class="outline">
	<div class="widget outline_inner">
	<?php if(Acl::check('hybrid'.$ds->id().'.document.edit')): ?>
	<?php echo Form::open(Request::current()->uri() . URL::query(array('id' => $doc->id)), array(
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
	<div class="spoiler-toggle-container widget-content-bg">
		<div class="spoiler-toggle text-center" data-spoiler=".spoiler-meta">
			<?php echo UI::icon( 'chevron-down spoiler-toggle-icon' ); ?> <span class="muted"><?php echo __('Metadata'); ?></span>
		</div>
		<div id="pageEditMetaMore" class="spoiler spoiler-meta">
			<br />

			<div class="control-group">
				<label class="control-label"><?php echo __('Meta title'); ?></label>
				<div class="controls">
					<?php echo Form::input('meta_title', $doc->meta_title, array(
						'class' => 'input-block-level'
					)); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo __('Meta keywords'); ?></label>
				<div class="controls">
					<?php echo Form::input('meta_keywords', $doc->meta_keywords, array(
						'class' => 'input-block-level'
					)); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo __('Meta description'); ?></label>
				<div class="controls">
					<?php echo Form::textarea('meta_description', $doc->meta_description, array(
						'class' => 'input-block-level'
					)); ?>
				</div>
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
		<?php echo UI::actions(TRUE, Route::get('datasources')->uri(array(
			'controller' => 'data',
			'directory' => 'datasources'
		)) . URL::query(array('ds_id' => $ds->id()), FALSE)); ?>
	</div>
	<?php echo Form::close(); ?>
	<?php else: ?>
	</div>
	<?php endif; ?>
</div></div>