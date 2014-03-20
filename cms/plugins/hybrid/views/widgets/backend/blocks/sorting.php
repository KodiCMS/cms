<?php
$fields = DataSource_Hybrid_Field_Factory::get_section_fields($ds_id);

$order_fields = array();
foreach ($doc_order as $data)
{
	$order_fields[key($data)] = $data[key($data)];
}

$selected_fields = array();
$available_fields = array();

$fields[] = DataSource_Hybrid_Field::factory('primitive_string', array(
	'family' => DataSource_Hybrid_Field::FAMILY_PRIMITIVE,
	'name' => 'header',
	'id' => 'header',
	'header' => __('Header')
));

$fields[] = DataSource_Hybrid_Field::factory('primitive_integer', array(
	'family' => DataSource_Hybrid_Field::FAMILY_PRIMITIVE,
	'name' => 'id',
	'id' => 'id',
	'header' => __('ID')
));

$fields[] = DataSource_Hybrid_Field::factory('primitive_datetime', array(
	'family' => DataSource_Hybrid_Field::FAMILY_PRIMITIVE,
	'name' => 'created_on',
	'id' => 'created_on',
	'header' => __('Created on')
));

foreach ($fields as $field)
{
	if( ! $field->is_sortable() ) continue;

	if(!isset($order_fields[$field->id]))
	{
		$available_fields[$field->id] = $field->header;
	}
	else
	{
		$ids[$field->id] = $field->header;
	}
}

foreach ($doc_order as $data)
{
	if(isset($ids[key($data)]))
		$selected_fields[key($data)] = (($data[key($data)] == Model_Widget_Decorator::ORDER_ASC) ? '+' : '-') .' '. $ids[key($data)];
}

?>
<script>
$(function() {
	var sf = $('#sf'),
		af = $('#af'),
		sf_cont = $('#sf-cont');
		
	var input = $('<input />');

	$('.sorting-btns button').click(function() { return false; });
	
	$('.btn-add').click(function() {
		var selected = $('option:selected', af)
			.remove();

		$(sf)
			.append(selected.text('+ ' + selected.text()))
	
		input.clone().attr({
			name: 'doc_order[]['+ selected.val() +']',
			value: 'ASC',
			type: 'hidden',
			id: 'sf_' + selected.val()
		}).appendTo(sf_cont);
	});

	$('.btn-remove').click(function() {
		var selected = $('option:selected', sf)
			.remove();
	
		$(af)
			.append(selected
				.text(selected.text().substr(2)))
		
		$('#sf_' + selected.val()).remove();
	});

	$('.btn-order').click(function() {
		var selected = $('option:selected', sf);
		
		if(selected.text().indexOf('+') > -1 ) {
			selected.text(selected.text().replace('+', '-'));
			$('#sf_' + selected.val()).val('DESC');
		} else {
			selected.text(selected.text().replace('-', '+'));
			$('#sf_' + selected.val()).val('ASC');
		}		
	});

	$('.btn-move').click(function() {
		var step = $(this).hasClass('up') ? -1 : 1;
		
		var index = $('option:selected', sf).index();
		
		to = index + step;
		
		if(index < 0 || to < 0 || !sf[0].options[to]) return;
		
		$('option:selected', sf).swapWith($('option:eq('+to+')', sf));
		$('option:eq('+to+')', sf).attr('selected', 'selected');
		
		console.log($('input[name^="doc_order"]', sf_cont));
		$('input[name^="doc_order"]', sf_cont).eq(index).swapWith($('input[name^="doc_order"]', sf_cont).eq(to));
	});
});

jQuery.fn.swapWith = function(to) {
    return this.each(function() {
        var copy_to = $(to).clone(true);
        var copy_from = $(this).clone(true);
        $(to).replaceWith(copy_from);
        $(this).replaceWith(copy_to);
    });
};
</script>
<div class="widget-header">
	<h4><?php echo __('Documents order'); ?></h4>
</div>
<div class="widget-content">

	<table class="table">
		<colgroup>
			<col width="220px" />
			<col width="110px" />
			<col />
		</colgroup>
		<tbody>
			<tr>
				<td>
					<?php echo __('Order by'); ?>
				</td>
				<td>
					
				</td>
				<td>
					<?php echo __('Available fields'); ?>
				</td>
			</tr>
			<tr>
				<td id="sf-cont">
					<?php echo Form::select('sf', $selected_fields, NULL, array(
						'size' => 5, 'class' => 'no-script', 'id' => 'sf'
					)); ?>
					
					<?php 
					foreach($doc_order as $data) 
					{
						echo Form::hidden('doc_order[]['.key($data).']', $data[key($data)], array(
							'id' => 'sf_' . key($data)
						));
					}
					?>
				</td>
				<td class="sorting-btns">
					<div class="btn-group btn-group-vertical span2">
						<?php echo UI::button('Add', array('class' => 'btn btn-add btn-block')); ?>
						<?php echo UI::button('Remove', array('class' => 'btn btn-remove btn-block')); ?>
						<?php echo UI::button('Move up', array('class' => 'btn btn-move up btn-block')); ?>
						<?php echo UI::button('Move down', array('class' => 'btn btn-move down btn-block')); ?>
						<?php echo UI::button('Asc / Desc', array('class' => 'btn btn-order btn-block')); ?>
					</div>
				</td>
				<td>
					<?php echo Form::select('af', $available_fields, NULL, array(
						'size' => 5, 'class' => 'no-script', 'id' => 'af'
					)); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>