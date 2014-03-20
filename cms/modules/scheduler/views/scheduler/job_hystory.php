<hr />
<h3><?php echo __('Job hystory'); ?></h3>
<div class="widget widget-nopad">
	<div class="widget-content">
		<table class="table table-striped table-hover">
			<colgroup>
				<col width="200px" />
				<col />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Job run time'); ?></th>
					<th><?php echo __('Status'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($hystory as $log): ?>
					<tr class="item">
						<td class="log-run-time">
							<?php echo Date::format($log->created_on, 'j F Y H:i:s'); ?>
						</td>
						<td class="job-status">
							<?php echo $log->status(); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>