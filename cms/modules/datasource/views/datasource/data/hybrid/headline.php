<table class="table table-striped">
	<colgroup>
		<col width="30px" />
		<?php foreach ($fields as $name => $width): ?>
		<col <?php if($width !== NULL) echo 'width="'.$width.'"px'; ?>/>
		<?php endforeach; ?>
	</colgroup>
	<thead>
		<tr>
			<th class="row-checkbox" id="cb-all"><?php echo Form::checkbox('doc[]'); ?></th>
			<?php foreach ($fields as $name => $width): ?>
			<th><?php echo __($name); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($data[1] as $id => $row): ?>
		<tr data-id="<?php echo $id; ?>" class="<?php echo !$row['published'] ? 'unpublished' : ''; ?>">
			<td class="row-checkbox"><?php echo Form::checkbox('doc[]', $id); ?></td>
			<td class="row-id"><?php echo $id; ?></td>
			<th class="row-header"><?php echo HTML::anchor('hybrid/document/view' . URL::query(array(
				'ds_id' => $ds_id, 'id' => $id
			)), $row['header']); ?></th>
			<?php if(isset($row['type'])): ?>
			<td class="row-type"><?php echo $row['type']; ?></td>
			<?php endif; ?>
			<td class="row-date"><?php echo $row['date']; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<th class="align-center" colspan="<?php echo count($fields) + 1; ?>"><?php echo __('Total doucments: :num', array(
				':num' => $data[0]
			)); ?></th>
		</tr>
	</tfoot>
</table>