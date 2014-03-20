<?php echo $toolbar; ?>

<div id="headline" class="widget">
	<div class="tablenav form-inline widget-header page-actions">
		<?php if(ACL::check($ds_type.$ds_id.'.document.edit')):?>
		<?php echo UI::button(__('Create Document'), array(
			'href' => Route::url('datasources', array(
				'controller' => 'document',
				'directory' => $ds_type,
				'action' => 'create'
			)) . URL::query(array('ds_id' => $ds_id)),
			'icon' => UI::icon( 'plus' )
		)); ?>
		<?php endif; ?>

		<?php if(ACL::check($ds_type.$ds_id.'.document.edit')):?>
		<div class="input-append pull-right">
			<?php echo Form::select('doc_actions', array(
				__('Actions'), 
				'remove' => __('Remove'), 
				'publish' => __('Publish'), 
				'unpublish' => __('Unpublish')), NULL, array(
				'id' => 'doc-actions', 'class' => 'input-medium no-script'
			)); ?>

			<?php echo UI::button(__('Apply'), array(
				'id' => 'apply-doc-action'
			)); ?>
		</div>
		<?php endif; ?>
		
		<div class="clearfix"></div>
	</div>
	
	<div class="widget-content widget-nopad">
	<?php echo $headline; ?>
	</div>
</div>