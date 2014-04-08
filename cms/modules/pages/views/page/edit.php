<script type="text/javascript">
	var PAGE_ID = <?php echo (int) $page->id; ?>;

	<?php if($action == 'add'): ?>
	$(function() {
		$('.spoiler-toggle').click();
	})
	<?php endif; ?>
</script>

<div id="pageEdit">
		<?php echo Form::open(Route::get('backend')->uri(array('controller' => 'page', 'action' => $action, 'id' => $action == 'add' ? $parent_id : $page->id)), array(
			'id' => 'pageEditForm', 'class' => Bootstrap_Form::HORIZONTAL, 'method' => Request::POST
		)); ?>
		<?php echo Form::hidden('token', Security::token()); ?>

		<?php if (!empty($parent_id)): ?>
		<?php echo Form::hidden('page[parent_id]', $parent_id); ?>
		<?php endif; ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<div id="pageEdit" class="box span9">
					<div class="widget ">
						<div id="pageEditMeta" class="widget-title">
							<?php echo View::factory('page/blocks/meta', array(
								'page' => $page,
								'action' => $action
							)); ?>
						</div>
						
						<?php Observer::notify('view_page_edit_plugins_top', $page); ?>
						<?php Observer::notify('view_page_edit_plugins', $page); ?>
						
						<?php if($action != 'add'): ?>
						<div class="widget-content ">
							<?php if( $page->loaded()): ?>
							
								<?php if (isset($page->updated_on)): ?>
								<?php echo UI::label(__('Last updated by :anchor on :date', array(
									':anchor' => HTML::anchor(Route::get('backend')->uri(array(
											'controller' => 'users',
											'action' => 'edit', 
											'id' => $page->updator->id
										)), $page->updator->username),
									':date' => Date::format($page->updated_on, 'D, j F Y'))), 'important'); ?>
								<?php endif; ?>

								<?php echo HTML::anchor($page->get_frontend_url(), UI::label(UI::icon('globe icon-white') . ' ' . __('View page')), array(
									'class' => 'item-preview', 'target' => '_blank'
								)); ?>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<div class="form-actions widget-footer">
							<?php echo UI::actions($page_name); ?>
						</div>
					</div>
				</div>

				<div class="span3">
					<?php Observer::notify('view_page_edit_sidebar_before', $page); ?>

					<div id="pageEditOptions" class="widget">
						<?php echo View::factory('page/blocks/sidebar', array(
							'page' => $page,
							'permissions' => $permissions,
							'action' => $action,
							'page_permissions' => $page_permissions
						)); ?>
					</div>

					<?php Observer::notify('view_page_edit_sidebar_after', $page); ?>
				</div>
			</div>
		</div>
	<?php echo Form::close(); ?>
</div>