<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if ( Acl::check( 'email.templates.add')): ?>
		<?php echo UI::button(__('Add template'), array(
			'href' => Route::get( 'email_controllers' )->uri(array('controller' => 'templates', 'action' => 'add')), 'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>
	</div>
	
	<div class="widget-content">
		<table class="table table-striped table-hover">
			<colgroup>
				<col />
				<col />
				<col width="200px" />
				<col width="200px" />
				<col width="100px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Email subject'); ?></th>
					<th><?php echo __('Email type'); ?></th>
					<th><?php echo __('Email from'); ?></th>
					<th><?php echo __('Email to'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($templates as $tpl): ?>
				<tr class="item">
					<td class="name">
						<?php if ( Acl::check( 'email.templates.edit')): ?>
						<?php echo HTML::anchor(Route::get('email_controllers')->uri(array(
							'controller' => 'templates',
							'action' => 'edit',
							'id' => $tpl->id
						)), $tpl->subject); ?>
						<?php else: ?>
						<?php echo UI::icon('lock'); ?> <?php echo $tpl->subject; ?>
						<?php endif; ?>
					</td>
					<td class="email_type">
						<?php if ( Acl::check( 'email.types.edit')): ?>
						<?php echo HTML::anchor(Route::get('email_controllers')->uri(array(
							'controller' => 'types',
							'action' => 'edit',
							'id' => $tpl->type->id
						)), $tpl->type->name); ?>
						<?php else: ?>
						<?php echo $tpl->type->name; ?>
						<?php endif; ?>
					</td>
					<td class="email"><?php echo UI::label($tpl->email_from); ?></td>
					<td class="email"><?php echo UI::label($tpl->email_to); ?></td>
					<td class="status"><?php echo $tpl->status; ?></td>
					<td class="actions">
						<?php if ( Acl::check( 'email.templates.delete')): ?>
						<?php echo UI::button(NULL, array(
							'href' => Route::get('email_controllers')->uri(array(
								'controller' => 'templates',
								'action' => 'delete',
								'id' => $tpl->id
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