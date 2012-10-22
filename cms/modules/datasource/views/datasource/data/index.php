<div id="headline">
	<div class="tablenav form-inline well page-actions">
		<?php echo UI::button(__('Create Document'), array(
			'href' => 'hybrid/document/create' . URL::query(array('ds_id' => $ds_id)),
			'icon' => UI::icon( 'plus' )
		)); ?>
		
		<div class="input-append pull-right">
			<?php echo Form::select('doc_actions', array(
				'Actions', 
				'remove' => 'Remove', 
				'publish' => 'Publish', 
				'unpublish' => 'Unpublish'), NULL, array(
				'id' => 'doc-actions', 'class' => 'input-medium'
			)); ?>

			<?php echo UI::button(__('Apply'), array(
				'id' => 'apply-doc-action'
			)); ?>
		</div>
	</div>
	<?php echo $headline; ?>
</div>