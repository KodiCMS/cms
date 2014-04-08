<div id="pageMap" class="widget widget-nopad">
	<div class="widget-header">
		<?php echo View::factory('page/blocks/search'); ?>

		<?php if ( Acl::check( 'page.add')): ?>
		<?php echo UI::button(__('Add page'), array(
			'id' => 'pageAddButton', 'class' => 'btn',
			'href' => Route::get('backend')->uri(array('controller' => 'page', 'action' => 'add')),
			'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>

		<?php if ( Acl::check( 'page.sort')): ?>
		<?php echo UI::button(__('Reorder'), array(
			'id' => 'pageMapReorderButton', 
			'class' => 'btn btn-primary',
			'icon' => UI::icon('move icon-white')
		)); ?>
		<?php endif; ?>

		<span class="clearfix"></span>
	</div>
	<div class="widget-content">
		<table id="pageMapHeader" class="table">
			<colgroup>
				<col />
				<col width="14%" />
				<col width="14%" />
				<col width="7%" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Page'); ?></th>
					<th class="align-right"><?php echo __('Date'); ?></th>
					<th class="align-right"><?php echo __('Status'); ?></th>
					<th class="align-right"><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
		</table>

		<ul id="pageMapItems" class="map-items page-items unstyled" data-level="0">
			<li data-id="<?php echo $page->id; ?>">
				<div class="item">
					<div class="row-fluid">
						<div class="title span7">
							<?php if( ! ACL::check('page.edit') OR ! AuthUser::hasPermission( $page->get_permissions() ) ): ?>
							<?php echo UI::icon('lock'); ?>
							<em title="/"><?php echo $page->title; ?></em>
							<?php else: ?>
							<?php 
							echo UI::icon('home') . ' '; 
							echo HTML::anchor( $page->get_url(), $page->title );
							?>
							<?php endif; ?>

							<?php echo $page->get_public_anchor(); ?>
						</div>
						<div class="actions offset4 span1">
							<?php if ( Acl::check( 'page.add')): ?>
							<?php echo UI::button(NULL, array(
								'icon' => UI::icon('plus'), 
								'href' => Route::get('backend')->uri(array('controller' => 'page', 'action' => 'add')),
								'class' => 'btn btn-mini')); ?>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<?php echo $content_children; ?>
			</li>
		</ul>
		<ul id="pageMapSearchItems" class="map-items page-items unstyled"></ul>
	</div>
</div>