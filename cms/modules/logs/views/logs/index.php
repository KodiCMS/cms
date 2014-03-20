<div class="row-fluid">
	<div class="span9">
		<div class="widget widget-nopad">
			<div class="widget-header">

			</div>
			<div class="widget-content">
				<table id="logs-table" class="table table-hover">
					<colgroup>
						<col width="150px" />
						<col width="100px" />
						<col />
						<col width="150px" />
					</colgroup>
					<thead>
						<tr>
							<th class="log-date"><?php echo __('Сreated on'); ?></th>
							<th class="log-level"><?php echo __('Log level'); ?></th>
							<th class="log-url"><?php echo __('Log url'); ?></th>
							<th class="log-user"><?php echo __('User'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($logs as $log): ?>
						<?php $class = Text::alternate('even', 'odd'); ?>
						<tr class="item <?php echo $class; ?>">
							<td class="log-date">
								<?php echo Date::format($log->created_on, 'j F y H:i'); ?>
							</td>
							<td class="log-level">
								<?php echo UI::label($log->level()); ?>
							</td>
							<td class="log-url">
								<?php echo $log->url(); ?>
							</td>
							<td class="log-user">
								<?php echo $log->user(); ?> <?php echo $log->ip(); ?>
							</td>
						</tr>
						<tr class="item-message <?php echo $class; ?>">
							<td class="log-message" colspan="4"><?php echo $log->message; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php echo $pager; ?>
	</div>
	<div class="span3">
		<?php echo $sidebar; ?>
	</div>
</div>



