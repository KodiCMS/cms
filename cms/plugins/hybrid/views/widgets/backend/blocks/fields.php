<?php $fields = DataSource_Hybrid_Field_Factory::get_section_fields($widget->ds_id); ?>
<div class="widget-header">
	<h4><?php echo __('Fetched document fields'); ?></h4>
</div>
<div class="widget-content widget-nopad">
	<table id="section-fields" class="table table-striped">
		<colgroup>
			<col width="30px" />
			<col width="100px" />
			<col width="200px" />
			<col />
		</colgroup>
		<tbody>
			<tr>
				<td class="f">
					<?php echo Form::checkbox('field[]', 'id', TRUE, array(
						'disabled' => 'disabled'
					)); ?>
				</td>
				<td class="sys">ID</td>
				<td>ID</td>
				<td></td>
			</tr>
			<tr>
				<td class="f">
					<?php echo Form::checkbox('field[]', 'header', TRUE, array(
						'disabled' => 'disabled'
					)); ?>
				</td>
				<td class="sys">header</td>
				<td><?php echo __('Header'); ?></td>
				<td></td>
			</tr>
			
			<?php foreach($fields as $field): ?>
			<tr id="field-<?php echo $field->name; ?>" class="field-<?php echo $field->family; ?>">
				<td class="f">
					<?php echo Form::checkbox('field['.$field->id.'][id]', $field->id, in_array($field->id, $widget->doc_fields)); ?>
				</td>
				<td class="sys">
					<?php echo substr($field->name, 2); ?>
				</td>
				<td>
					<?php echo HTML::anchor('/backend/hybrid/field/edit/' . $field->id, $field->header, array('target' => '_blank', 'class' => 'popup fancybox.iframe') ); ?>
				</td>
				<td>
					<?php
						$types = $field->widget_types();
						if($types !== NULL)
						{
							$widgets = $widget->get_related_widgets($field->widget_types());

							if(isset($widgets[$widget->id])) unset($widgets[$widget->id]);

							if( ! empty($widgets) )
							{
								$widgets = array(__('--- Not set ---')) + $widgets;

								$selected = NULL;
								if(isset($widget->doc_fetched_widgets[$field->id]))
								{
									$selected = $widget->doc_fetched_widgets[$field->id];
								}

								echo Form::select('field['.$field->id.'][fetcher]', $widgets, $selected); 
							}
						}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>