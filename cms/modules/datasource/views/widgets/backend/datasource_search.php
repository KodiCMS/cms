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

<div class="widget-header">
	<h4><?php echo __('Search in sources'); ?></h4>
</div>
<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'sources[]', 'options' => $widget->sources()
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->sources)
			->label(__('Datasources'))
		));
	?>
</div>
<div class="widget-content ">
	<?php foreach ($widget->sources() as $id => $header): if(!in_array($id, $widget->sources)) continue; ?>
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'source_hrefs['.$id.']', 'value' => Arr::get($widget->source_hrefs, $id)
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->label($header)
		));
	?>
	<?php endforeach; ?>
</div>

