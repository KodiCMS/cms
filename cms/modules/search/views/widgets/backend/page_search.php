<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'search_key', 'value' => $widget->search_key
			))
			->label(__('Search key ($_GET)'))
		));
	?>
</div>