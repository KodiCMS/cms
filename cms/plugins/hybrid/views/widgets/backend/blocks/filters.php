<script>
	var add_filter = function() {
		var filter = $('#sample_filter .filter')
			.clone()
			.appendTo(filters_container);
	
		$('.select2-container', filter).remove()
		$("select", filter).select2();
			
		filter.on('click', '.remove_filter', function() {
			filter.remove();
			return false;
		});
		
		return filter;
	}
	
	var set_condition = function(filters_container, data) {
		$('.select2-container', filters_container).remove()
		$("select", filters_container).select2();
		
		for(key in data) {
			if(key == 'invert' && data[key] == 1) {
				$('input[name="doc_filter[' + key +'][]"]', filters_container).check()
				continue;
			}
			$('input[name="doc_filter[' + key +'][]"]', filters_container).val(data[key]);
			$('select[name="doc_filter[' + key +'][]"]', filters_container).val(data[key]).trigger("change");
			
			if(key == 'field') {
				$(filters_container).find('.field-title').text(data[key]).show();
			}
		}
		
	}
	
	$(function() {
		var filters_container = $('#filters_container');
		$('#add_filter').on('click', function() {
			var filter = add_filter();
			
			$('input[name="doc_filter[field][]"]', filter).on('keyup', function() {
				var field_title = filter.find('.field-title');
				
				if(!field_title.text()) field_title.hide();
				else field_title.show();

				field_title.text($(this).val());
			});

			return false;
		});
	})
</script>

<div class="widget-header">
	<h4><?php echo __('Filters'); ?></h4>
</div>

<div class="widget-content">
	<fieldset disabled id="sample_filter" class="hide">
		<div class="well filter">
			<div class="clearfix"></div>
			<h4 class="field-title hide pull-left"></h4>
			
			<?php echo UI::button(NULL, array('icon' => UI::icon('trash'), 'class' => 'btn btn-danger btn-mini remove_filter pull-right')); ?>
			<div class="clearfix"></div>
			<table style="width: 100%">
				<colgroup>
					<col width="100px" />
					<col />
				</colgroup>
				<tbody>
					<tr>
						<td><?php echo __('Where field')?></td>
						<td>
							<?php echo Form::input('doc_filter[field][]', NULL, array(
								'class' => Bootstrap_Form_Element_Input::MEDIUM
							)); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo __('Condition')?></td>
						<td>
							<?php echo Form::select('doc_filter[condition][]', array(
								DataSource_Hybrid_Agent::COND_EQ => __('Equal'),
								DataSource_Hybrid_Agent::COND_BTW => __('Between'),
								DataSource_Hybrid_Agent::COND_GT => __('Greater than'),
								DataSource_Hybrid_Agent::COND_LT => __('Less than'),
								DataSource_Hybrid_Agent::COND_GTEQ => __('Greater than or equal'),
								DataSource_Hybrid_Agent::COND_LTEQ => __('Less than or equal'),
								DataSource_Hybrid_Agent::COND_CONTAINS => __('Contains'),
								DataSource_Hybrid_Agent::COND_LIKE => __('Like')
							)); ?>
							<label class="inline checkbox">
								<?php echo Form::checkbox('doc_filter[invert][]', 1, FALSE); ?>
								<?php echo __('Invert condition'); ?>
							</label>
							
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo __('Conition value')?></td>
						<td>
							<?php echo Form::select('doc_filter[type][]', array(
								DataSource_Hybrid_Agent::VALUE_CTX => __('Context'),
								DataSource_Hybrid_Agent::VALUE_PLAIN => __('Plain')
							)); ?>
							<?php echo Form::input('doc_filter[value][]', NULL, array(
								'class' => Bootstrap_Form_Element_Input::MEDIUM
							)); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<div id="filters_container"></div>
	
	<?php 
	if(!empty($widget->doc_filter))
	{
		echo '<script> $(function(){';
		foreach($widget->doc_filter as $filter)
			echo 'set_condition(add_filter(), ' . json_encode( $filter ) . '); ';
		echo '});</script>';
	}
	?>
	
	<?php echo UI::button(__('Add filter'), array('icon' => UI::icon('plus'), 'id' => 'add_filter', 'class' => 'btn')); ?>
</div>