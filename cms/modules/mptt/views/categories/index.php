<div class="widget widget-nopad">
	<div class="widget-header">
		<?php if ( Acl::check( 'categories.add')): ?>
		<?php echo UI::button(__('Add category'), array(
			'class' => 'btn',
			'href' => Route::url('backend', array('controller' => 'categories', 'action' => 'add')),
			'icon' => UI::icon('plus')
		)); ?>
		<?php endif; ?>

		<?php if ( Acl::check( 'categories.sort') AND $categories->count() > 0): ?>
		<?php echo UI::button(__('Reorder'), array(
			'id' => 'categoriesReorderBtn', 
			'class' => 'btn btn-primary',
			'icon' => UI::icon('move icon-white')
		)); ?>
		<?php endif; ?>

		<span class="clearfix"></span>
	</div>
	<div class="widget-content">
		<table class="table">
			<colgroup>
				<col />
				<col width="7%" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Category'); ?></th>
					<th class="align-right"><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
		</table>

		<ul class="map-items unstyled" data-level="0">
			<?php foreach ($categories as $category): ?>
			<?php if($category->is_root()): ?>
			<li data-id="<?php echo $category->id; ?>">
				<div class="item">
					<div class="row-fluid">
						<div class="title span7">
							<?php if( ! ACL::check('categories.edit') ): ?>
							<?php echo UI::icon('lock'); ?>
							<em title="/"><?php echo $category->name; ?></em>
							<?php else: ?>
							<?php echo UI::icon('home') . ' ' . HTML::anchor( Route::url('backend', array(
								'controller' => 'categories', 'action' => 'edit', 'id' => $category->id
							) ), $category->name ); ?>
							<?php endif; ?>
						</div>
						<div class="actions offset4 span1">
							<?php if ( Acl::check( 'categories.add')): ?>
							<?php echo UI::button(NULL, array(
								'icon' => UI::icon('plus'), 
								'href' => Route::url('backend', array('controller' => 'categories', 'action' => 'add', 'id' => $category->id)),
								'class' => 'btn btn-mini')); ?>
							<?php endif; ?>
							
							<?php if (Acl::check( 'categories.delete')): ?>
							<?php echo UI::button(NULL, array(
								'href' => Route::url('backend', array(
									'controller' => 'categories',
									'action' => 'delete',
									'id' => $category->id
								)), 'icon' => UI::icon('remove icon-white'), 
								'class' => 'btn btn-mini btn-confirm btn-danger'
							)); ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</li>
			<?php else: ?>
			<li data-id="<?php echo $category->id; ?>">
				<div class="item">
					<div class="row-fluid">
						<div class="title span7">
							<?php if( ! ACL::check('categories.edit') ): ?>
							<?php echo UI::icon('lock'); ?>
							<em title="/"><?php echo $category->name; ?></em>
							<?php else: ?>
							<?php echo str_repeat('&nbsp;', $category->lvl * 7) . UI::icon('angle-right') .'  ' . HTML::anchor( Route::url('backend', array(
								'controller' => 'categories', 'action' => 'edit', 'id' => $category->id
							) ), $category->name ); ?>
							<?php endif; ?>
						</div>
						<div class="actions offset4 span1">
							<?php if ( Acl::check( 'categories.add')): ?>
							<?php echo UI::button(NULL, array(
								'icon' => UI::icon('plus'), 
								'href' => Route::url('backend', array('controller' => 'categories', 'action' => 'add', 'id' => $category->id)),
								'class' => 'btn btn-mini')); ?>
							<?php endif; ?>
							
							<?php if (Acl::check( 'categories.delete')): ?>
							<?php echo UI::button(NULL, array(
								'href' => Route::url('backend', array(
									'controller' => 'categories',
									'action' => 'delete',
									'id' => $category->id
								)), 'icon' => UI::icon('remove icon-white'), 
								'class' => 'btn btn-mini btn-confirm btn-danger'
							)); ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		
		<ul id="categoriesSortContainer" class="map-items page-items unstyled"></ul>
	</div>
</div>