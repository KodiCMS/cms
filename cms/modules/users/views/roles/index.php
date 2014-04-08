<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if ( Acl::check('roles.add')): ?>
		<?php echo UI::button(__('Add role'), array(
			'href' => Route::get( 'backend')->uri(array('controller' => 'roles', 'action' => 'add')), 'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>
	</div>
	
	<div class="widget-content">
		<table class="table table-striped table-hover">
			<colgroup>
				<col width="150px" />
				<col />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Name'); ?></th>
					<th><?php echo __('Description'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($roles as $role): ?>
				<tr class="item">
					<td class="name">
						<?php echo UI::icon('unlock-alt'); ?>
						<?php if ( Acl::check( 'roles.edit')): ?>
						<?php echo HTML::anchor(Route::get('backend')->uri(array(
							'controller' => 'roles',
							'action' => 'edit',
							'id' => $role->id
						)), $role->name); ?>
						<?php else: ?>
						<?php echo UI::icon('lock'); ?> <?php echo $role->name; ?>
						<?php endif; ?>
					</td>
					<td class="description">
						<?php echo __($role->description); ?>
					</td>
					<td class="actions">
						<?php 
						if ($role->id > 2 AND ACL::check('roles.delete'))
						{
							echo UI::button(NULL, array(
								'href' => Route::get('backend')->uri(array(
									'controller' => 'roles',
									'action' => 'delete',
									'id' => $role->id
								)), 
								'icon' => UI::icon('remove'),
								'class' => 'btn btn-mini btn-confirm'
							));
						} ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<?php echo $pager; ?>