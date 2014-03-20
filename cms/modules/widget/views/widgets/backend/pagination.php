<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'related_widget_id', 'options' => $select
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->related_widget_id)
			->label(__('Widget'))
		));
	?>
	
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'query_key', 'value' => $widget->get('query_key')
			))
			->label(__('Query key (GET)'))
		));
	?>
</div>