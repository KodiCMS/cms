<?php echo Form::hidden($field->name, $value['id'], array(
	'id' => $field->name,
	'data-header' => $value['header'],
	'data-type' => $field->type,
	'data-ds' => $field->from_ds,
	'data-hid' => $doc->id,
	'dadta-fid' => $field->id
)); ?>

<?php echo UI::button($value['header'], array(
	'target' => 'blank', 
	'icon' => UI::icon('file'),
	'href' => $field->type . '/document/view' . URL::query(array(
		'ds_id' => $field->from_ds, 'id' => $value['id']
	), FALSE)
)); ?>
