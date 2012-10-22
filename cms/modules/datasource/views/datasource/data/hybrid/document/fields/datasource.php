<tr>
	<th><?php echo $field->header; ?></th>
	<td>
		<?php if($field->from_ds > 0): ?>
			<?php echo HTML::anchor('datasources/data' . URL::query(array(
				'ds_id' => $field->from_ds, 'target' => 'blank'
			), FALSE), __('Manage datasource')); ?>
		<?php else: ?>
			<?php echo __('Manage datasource'); ?>
		<?php endif; ?>
	</td>
</tr>