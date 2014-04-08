<div class="widget-header">
	<h4><?php echo __('Datasource Fields'); ?></h4>
</div>
<div class="widget-content widget-nopad">
	<table id="section-fields" class="table table-striped table-hover">
		<colgroup>
			<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
			<col width="30px" />
			<?php endif; ?>
			<col width="50px" />
			<col width="100px" />
			<col width="200px" />
			<col width="100px" />
			<col />
		</colgroup>
		<thead>
			<tr>
				<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
				<th></th>
				<?php endif; ?>
				<th><?php echo __('Field position'); ?></th>
				<th><?php echo __('Field key'); ?></th>
				<th><?php echo __('Field header'); ?></th>
				<th><?php echo __('Field type'); ?></th>
				<th><?php echo __('Show in headline'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
				<td class="f">
					<?php echo Form::checkbox('field[]', 'id', FALSE, array(
						'disabled' => 'disabled'
					)); ?>
				</td>
				<?php endif; ?>
				<td class="position">0</td>
				<td class="sys">ID</td>
				<td>ID</td>
				<td><?php echo UI::label('integer'); ?></td>
				<td><?php echo Form::checkbox('', 1, TRUE, array('disabled' => 'disabled')); ?></td>
			</tr>
			<tr>
				<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
				<td class="f">
					<?php echo Form::checkbox('field[]', 'header', FALSE, array(
						'disabled' => 'disabled'
					)); ?>
				</td>
				<?php endif; ?>
				<td class="position">0</td>
				<td class="sys">header</td>
				<td><?php echo __('Header'); ?></td>
				<td><?php echo UI::label('string'); ?></td>
				<td><?php echo Form::checkbox('', 1, TRUE, array('disabled' => 'disabled')); ?></td>
			</tr>

			<?php foreach($record->fields() as $f): ?>
			<tr id="field-<?php echo $f->name; ?>">
				<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
				<td class="f">
					<?php 
					$attrs = array('id' => $f->name);
					if($f->ds_id != $ds->id()) $attrs['disabled'] = 'disabled';
					echo Form::checkbox('field[]', $f->name, FALSE, $attrs); ?>

				</td>
				<?php endif; ?>
				<td class="position"><?php echo $f->position; ?></td>
				<td class="sys">
					<label for="<?php echo $f->name; ?>">
						<?php echo substr($f->name, 2); ?>
					</label>
				</td>
				<td>
					<?php if(Acl::check($ds->type().$ds->id().'.field.edit')): ?>
					<?php echo HTML::anchor(Route::get('datasources')->uri(array(
						'controller' => 'field',
						'directory' => 'hybrid',
						'action' => 'edit',
						'id' => $f->id
					)), $f->header  ); ?>
					<?php else: ?>
					<strong><?php echo $f->header; ?> </strong>
					<?php endif; ?>
				</td>
				<td>
					<?php echo UI::label($f->type); ?>
				</td>
				<td>
					<?php 
					$attrs = array();
					if(!Acl::check($ds->type().$ds->id().'.field.edit')) $attrs['disabled'] = 'disabled';
					
					echo Form::checkbox('in_headline['.$f->id.']', 1, (bool) $f->in_headline, $attrs); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div class="widget-header">
	<div class="btn-group">
		<?php if(Acl::check($ds->type().$ds->id().'.field.edit')): ?>
		<?php echo UI::button(__('Add field'), array(
			'href' => Route::get('datasources')->uri(array(
				'controller' => 'field',
				'directory' => 'hybrid',
				'action' => 'add',
				'id' => $ds->id()
			)),
			'icon' => UI::icon('plus'),
			'class' => 'btn fancybox'
		)); ?>
		<?php endif; ?>
		
		<?php if(Acl::check($ds->type().$ds->id().'.field.remove')): ?>
		<?php echo UI::button(__('Remove fields'), array(
			'icon' => UI::icon('minus icon-white'), 'id' => 'remove-fields',
			'class' => 'btn btn-danger'
		)); ?>
		<?php endif; ?>
	</div>
</div>
<?php echo View::factory('widgets/backend/blocks/sorting', array(
	'ds_id' => $ds->id(),
	'doc_order' => $ds->headline()->sorting()
));?>

