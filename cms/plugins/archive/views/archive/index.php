<div class="widget widget-nopad">
	<div class="widget-header">
		<?php echo UI::button(__('Add page'), array(
			'href' => 'page/add/'.$page->id, 'icon' => UI::icon('plus')
		)); ?>
	</div>

	<div class="widget-content">
		<table class="table table-striped" id="SnippetList">
			<colgroup>
				<col />
				<col width="150px" />
				<col width="150px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Page'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Date'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($items as $item): ?>
				<tr data-id="<?php echo $item->id; ?>">
					<th class="title">
						<?php if( ! AuthUser::hasPermission($item->getPermissions()) ): ?>
						<img src="images/page-text-locked.png" title="<?php echo('You do not have permission to access the requested page!'); ?>" />
						<em title="/<?php echo $item->getUri(); ?>"><?php echo $item->title; ?></em>
						<?php else: ?>
						<?php echo UI::icon('file'); ?>
						<?php echo HTML::anchor('page/edit/'.$item->id, $item->title); ?>
						<?php endif; ?>
					</th>
					<td class="date">
						<?php echo Date::format($item->published_on); ?>
					</td>
					<td class="status">
						<?php switch ($item->status_id):
							case Model_Page::STATUS_DRAFT:    echo UI::label(__('Draft'), 'info');       break;
							case Model_Page::STATUS_REVIEWED: echo UI::label(__('Reviewed'), 'info'); break;
							case Model_Page::STATUS_HIDDEN:   echo UI::label(__('Hidden'), 'default');     break;
							case Model_Page::STATUS_PUBLISHED:
								if( strtotime($item->published_on) > time() )
									echo UI::label(__('Pending'), 'success');
								else
									echo UI::label(__('Published'), 'success');
							break;
						endswitch; ?>
					</td>
					<td class="actions">
						<?php 
						if( AuthUser::hasPermission($item->getPermissions()) )
						{
							echo UI::button(NULL, array(
								'href' => 'page/delete/'.$item->id, 'icon' => UI::icon('remove'),
								'class' => 'btn btn-mini btn-confirm'
							));
						}?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php echo $pager; ?>