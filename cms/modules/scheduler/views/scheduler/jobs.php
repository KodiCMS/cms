<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if ( Acl::check( 'scheduler.add')): ?>
		<?php echo UI::button(__('Add job'), array(
			'href' => Route::get('backend')->uri(array('controller' => 'scheduler', 'action' => 'add')), 'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>
	</div>
	
	<div class="widget-content">
		<table class="table table-striped table-hover">
			<colgroup>
				<col />
				<col width="150px" />
				<col width="100px" />
				<col width="170px" />
				<col width="170px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Job name'); ?></th>
					<th><?php echo __('Job function'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Last run'); ?></th>
					<th><?php echo __('Next run'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($jobs as $job): ?>
					<tr class="item">
						<td class="job-name">
							<?php if ( Acl::check( 'scheduler.edit')): ?>
							<?php echo HTML::anchor(Route::get('backend')->uri(array(
								'controller' => 'scheduler',
								'action' => 'edit',
								'id' => $job->id
							)), $job->name); ?>
							<?php else: ?>
							<?php echo UI::icon('lock'); ?> <?php echo $job->name; ?>
							<?php endif; ?>
						</td>
						<td class="job-function">
							<?php echo UI::label($job->job); ?>
						</td>
						<td class="job-status">
							<?php echo $job->status(); ?>
						</td>
						<td class="job-last-run">
							<?php echo Date::format($job->date_last_run, 'j/m/Y H:i:s'); ?>
						</td>
						<td class="job-interval">
							<?php echo Date::format($job->date_next_run, 'j/m/Y H:i:s'); ?>
						</td>
						<td class="actions">
							<?php if ( Acl::check( 'scheduleremail.templates.delete')): ?>
							<?php echo UI::button(NULL, array(
								'href' => Route::get('backend')->uri(array(
									'controller' => 'scheduler',
									'action' => 'delete',
									'id' => $job->id
								)), 
								'icon' => UI::icon('remove'),
								'class' => 'btn btn-mini btn-danger btn-confirm'
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
