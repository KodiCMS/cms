<div class="widget widget-nopad">
	<div class="widget-header">
		<?php echo UI::button(__('Add page'), array(
			'href' => Route::get('backend')->uri(array(
				'controller' => 'page',
				'action' => 'add',
				'id' => $page->id
			)), 'icon' => UI::icon('plus')
		)); ?>
	</div>

	<div class="widget-content">
		<table class="table table-striped" id="SnippetList">
			<colgroup>
				<col />
				<col width="150px" />
				<col width="150px" />
				<col width="150px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Page'); ?></th>
					<th><?php echo __('Public link'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Date'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($items as $page): ?>
				<tr data-id="<?php echo $page->id; ?>">
					<th class="title">
						<?php if( ! ACL::check('page.edit') OR ! AuthUser::hasPermission( $page->get_permissions() ) ): ?>
						<?php echo UI::icon('lock'); ?>
						<em title="/"><?php echo $page->title; ?></em>
						<?php else: ?>
						<?php 
						echo UI::icon('file') . ' '; 
						echo HTML::anchor( $page->get_url(), $page->title );
						?>
						<?php endif; ?>
					</th>
					<td class="public_link">
						<?php echo $page->get_public_anchor(); ?>
					</td>
					<td class="status">
						<?php echo $page->get_status(); ?>
					</td>
					<td class="date">
						<?php echo Date::format($page->published_on); ?>
					</td>
					<td class="actions">
						<?php if (Acl::check( 'page.delete')): ?>
						<?php echo UI::button(NULL, array(
							'href' => Route::get('backend')->uri(array(
								'controller' => 'page',
								'action' => 'delete',
								'id' => $page->id
							)), 'icon' => UI::icon('remove icon-white'), 
							'class' => 'btn btn-mini btn-confirm btn-danger'
						)); ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php echo $pager; ?>