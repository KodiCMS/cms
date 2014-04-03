<div class="widget-content">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Textarea::factory(array(
				'name' => 'allowed_tags', 'body' => $widget->get('allowed_tags'),
			))
			->attributes('rows', 2)
			->label(__('Allowed tags'))
		));
	?>
	
	<hr />
	
	<div class="control-group">
		<label class="control-label" for="next_url"><?php echo __('Next page after success send (URL)'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'next_url', $widget->next_url, array(
				'class' => 'input-xxlarge'
			)); ?>
		</div>
	</div>
</div>

<div class="widget-header">
	<h4><?php echo __('Message fields'); ?></h4>
</div>

<script>
	var add_field = function() {
		return $('#sample_field .field')
			.clone()
			.appendTo(fields_container);
	}

	var set_field = function(field_container, data) {
		$('.select2-container', field_container).remove()
		$("select", field_container).select2();
			
		for(key in data) {
			$('input[name="field[' + key +'][]"]', field_container).val(data[key]);
			$('select[name="field[' + key +'][]"]', field_container).val(data[key]).trigger("change");
			
			if(key == 'id') {
				$(field_container).find('.field-title').text(data[key]);
			}
		}
		
		$('input[name="field[id][]"]', field_container).on('keyup', function() {
			field_container.find('.field-title').text($(this).val());
		});
	}
	$(function() {
		var fields_container = $('#fields_container');
		$('#add_field').on('click', function() {
			var field = add_field();

			$('.select2-container', field).remove()
			$("select", field).select2();
			
			$('input[name="field[id][]"]', field).on('keyup', function() {
				field.find('.field-title').text($(this).val());
			});

			return false;
		});
		
		$('#fields_container').on('click', '.remove_field', function() {
			$(this).parents('.field').remove();
		});
	})
</script>
<div class="widget-content " id="fields">
	<div id="sample_field" class="hide">
		<div class="well field">
			<h4 class="field-title"></h4>
			<table style="width: 100%">
				<colgroup>
					<col width="200px" />
					<col width="260px" />
					<col width="200px" />
					<col />
				</colgroup>
				<tbody>
					<tr>
						<td></td>
						<td><?php echo __('Field source')?></td>
						<td><?php echo __('Field source key')?></td>
						<td nowrap><?php echo __('Field value type')?></td>
						<td rowspan="7" valign="top">
							<?php echo UI::button(NULL, array('icon' => UI::icon('trash'), 'class' => 'btn btn-danger btn-mini remove_field')); ?>
						</td>
					</tr>
					<tr>
						<td><h5><?php echo __('Field'); ?></h5></td>
						<td>
							<?php echo Form::select('field[source][]', $widget->src_types()); ?>
						</td>
						<td>
							<?php echo Form::input('field[id][]', NULL, array(
								'class' => Bootstrap_Form_Element_Input::MEDIUM
							)); ?>
						</td>
						<td>
							<?php echo Form::select('field[type][]', $widget->value_types()); ?>
						</td>
					</tr>
					
					<tr>
						<td><h5><?php echo __('Field name'); ?></h5></td>
						<td colspan="3">
							<?php echo Form::input('field[name][]', NULL, array(
								'class' => Bootstrap_Form_Element_Input::MEDIUM
							)); ?>
						</td>
					</tr>

					<tr><td colspan="4"><hr /></td></tr>

					<tr>
						<td></td>
						<td nowrap><?php echo __('Validation'); ?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td><h5><?php echo __('Validator'); ?></h5></td>
						<td colspan="3">
							<?php echo Form::input('field[validator][]'); ?>
							<br /><br />
							<span class="flags" data-append="true">
								<span class="label" data-value="not_empty"><?php echo __('Not empty'); ?></span>
								<span class="label" data-value="url"><?php echo __('URL'); ?></span>
								<span class="label" data-value="phone"><?php echo __('Phone number'); ?></span>
								<span class="label" data-value="email"><?php echo __('Email'); ?></span>
								<span class="label" data-value="email_domain"><?php echo __('Email domain'); ?></span>
								<span class="label" data-value="ip"><?php echo __('IP'); ?></span>
								<span class="label" data-value="credit_card"><?php echo __('Credit card'); ?></span>
								<span class="label" data-value="date"><?php echo __('Date'); ?></span>
								<span class="label" data-value="alpha"><?php echo __('Alpha'); ?></span>
								<span class="label" data-value="alpha_dash"><?php echo __('Alpha and hyphens'); ?></span>
								<span class="label" data-value="alpha_numeric"><?php echo __('Alpha and numbers'); ?></span>
								<span class="label" data-value="digit"><?php echo __('Integer digit'); ?></span>
								<span class="label" data-value="decimal"><?php echo __('Decimal'); ?></span>
								<span class="label" data-value="numeric"><?php echo __('Numeric'); ?></span>
								<span class="label" data-value="color"><?php echo __('Color'); ?></span>
							</span>
						</td>
					</tr>
					<tr><td colspan="4"><hr /></td></tr>
					<tr>
						<td></td>
						<td nowrap><?php echo __('Field error message'); ?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td><h5><?php echo __('Field error'); ?></h5></td>
						<td colspan="3">
							<?php echo Form::input('field[error][]', NULL, array(
								'class' => Bootstrap_Form_Element_Input::BLOCK_LEVEL
							)); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<?php 
	if(!empty($widget->fields))
	{
		echo '<script> $(function(){';
		foreach($widget->fields as $field)
			echo 'set_field(add_field(), ' . json_encode( $field ) . '); ';
		echo '});</script>';
	}
	?>
	
	<div id="fields_container"></div>
	<?php echo UI::button(__('Add field'), array('icon' => UI::icon('plus'), 'id' => 'add_field', 'class' => 'btn btn-large')); ?>
</div>