<div class="widget-content">
	<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Input::factory(array(
			'name' => 'next_url', 'value' => $widget->get('next_url', '/')
		))
		->label(__('Next page (URI)'))
		->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
	)); ?>
</div>