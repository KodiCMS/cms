<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if( ACL::check( 'layout.add')): ?>
		<?php echo UI::button(__('Add layout'), array(
			'icon' => UI::icon( 'plus' ), 'href' => Route::get('backend')->uri(array('controller' => 'layout', 'action' => 'add')),
		)); ?>
		<?php endif; ?>

		<?php if( ACL::check( 'layout.rebuild')): ?>
		<?php echo UI::button(__('Rebuild blocks'), array(
			'icon' => UI::icon( 'refresh' ), 'href' => Route::get('backend')->uri(array('controller' => 'layout', 'action' => 'rebuild')),
			'class' => 'btn btn-inverse btn-mini'
		)); ?>
		<?php endif; ?>
	</div>

	<div class="widget-content">
		<table class=" table table-striped table-hover" id="LayoutList">
			<colgroup>
				<col />
				<col width="150px" />
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Layout name'); ?></th>
					<th><?php echo __('Modified'); ?></th>
					<th><?php echo __('Size'); ?></th>
					<th><?php echo __('Direction'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($layouts as $layout): ?>
				<tr>
					<th class="name">
						<?php echo UI::icon( 'desktop' ); ?>

						<?php if( ! $layout->is_writable()): ?>
						<span class="label label-warning"><?php echo __('Read only'); ?></span>
						<?php endif; ?>
						
						<?php if( ACL::check( 'layout.edit') OR ACL::check( 'layout.view')): ?>
						<?php echo HTML::anchor(Route::get('backend')->uri(array('controller' => 'layout', 'action' => 'edit', 'id' => $layout->name)), $layout->name, array('class' => ! $layout->is_writable() ? 'popup fancybox.iframe' : '')); ?>
						<?php else: ?>
						<?php echo UI::icon('lock'); ?> <?php echo $layout->name; ?>
						<?php endif; ?>
						<?php if(count($layout->blocks()) > 0): ?>
						<span class="muted">
							<?php echo __('Layout blocks'); ?>: <?php echo implode(', ', $layout->blocks()); ?>
						</span>
						<?php endif; ?>
					</th>
					<td class="modified">
						<?php echo Date::format($layout->modified()); ?>
					</td>
					<td class="size">
						<?php echo Text::bytes( $layout->size()); ?>
					</td>
					<td class="direction">
						<?php echo UI::label($layout->get_relative_path()); ?>
					</td>
					<td class="actions">
						<?php if( ACL::check( 'layout.delete')): ?>
						<?php echo UI::button(NULL, array(
							'icon' => UI::icon( 'remove' ),
							'href' => Route::get('backend')->uri(array('controller' => 'layout', 'action' => 'delete', 'id' => $layout->name)),
							'class' => 'btn btn-mini btn-confirm'
						)); ?>
						<?php endif; ?>
					</td>
				</tr>

				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>