<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if ( Acl::check( 'email.types.add')): ?>
		<?php echo UI::button(__('Add email type'), array(
			'href' => Route::get( 'email_controllers')->uri(array('controller' => 'types', 'action' => 'add')), 'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>
	</div>
	
	<div class="widget-content">
		<table class="table table-striped table-hover">
			<colgroup>
				<col />
				<col width="200px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Email type name'); ?></th>
					<th><?php echo __('Email type code'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($types as $type): ?>
				<tr class="item">
					<td class="name">
						<?php if ( Acl::check( 'email.types.edit')): ?>
						<?php echo HTML::anchor(Route::get('email_controllers')->uri(array(
							'controller' => 'types',
							'action' => 'edit',
							'id' => $type->id
						)), $type->name); ?>
						<?php else: ?>
						<?php echo UI::icon('lock'); ?> <?php echo $type->name; ?>
						<?php endif; ?>
					</td>
					<td class="email_type">
						<?php echo UI::label($type->code); ?>
					</td>
					<td class="actions">
						<?php if ( Acl::check( 'email.types.delete')): ?>
						<?php echo UI::button(NULL, array(
							'href' => Route::get('email_controllers')->uri(array(
								'controller' => 'types',
								'action' => 'delete',
								'id' => $type->id
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