<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'page_id', 'options' => $select
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->page_id)
			->label(__('Page'))
		));
	?>
</div>