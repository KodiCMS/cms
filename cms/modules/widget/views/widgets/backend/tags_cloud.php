<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'min_size', 'value' => $widget->min_size
			))
			->label(__('Min font-size (px)'))
		));
	?>
	
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'max_size', 'value' => $widget->max_size
			))
			->label(__('Max font-size (px)'))
		));
	?>
	<hr />
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'order_by', 'options' => array(
					'name_asc' => __('Tag name A-Z'),
					'name_desc' => __('Tag name Z-A'),
					'count_asc' => __('Count tags 0-9'),
					'count_desc' => __('Count tags 9-0'),
				)
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->order_by)
			->label(__('Order by'))
		));
	?>
	
</div>