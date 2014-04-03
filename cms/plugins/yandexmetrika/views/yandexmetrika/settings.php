<div class="widget-header">
	<h3><?php echo __('General settings'); ?></h3>
</div>
<div class="widget-content">
	<div class="control-group">
		<?php echo Form::label('setting_counter_id', __('Metrics ID'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[counter_id]', $plugin->get('counter_id', 00000000), array(
				'id' => 'setting_counter_id', 'class' => '', 'maxlength' => 20, 'size' => 20
			)); ?>
		</div>
	</div>
</div>

<div class="widget-header">
	<h3><?php echo __('Yandex metrika settings'); ?></h3>
</div>

<div class="widget-content">
	<div class="control-group">
		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Checkbox::factory(array(
					'name' => 'setting[webvisor]', 'value' => 1
				))
				->checked($plugin->webvisor == 1)
				->label(__('WebVisor'))
			));
		?>
		
		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Checkbox::factory(array(
					'name' => 'setting[clickmap]', 'value' => 1
				))
				->checked($plugin->clickmap == 1)
				->label(__('Click map'))
			));
		?>
		
		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Checkbox::factory(array(
					'name' => 'setting[track_links]', 'value' => 1
				))
				->checked($plugin->track_links == 1)
				->label(__('External links, file downloads and "Share" button report'))
			));
		?>

		<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Checkbox::factory(array(
					'name' => 'setting[accurate_track_bounce]', 'value' => 1
				))
				->checked($plugin->accurate_track_bounce == 1)
				->label(__('Accurate bounce rate'))
			));
		?>
	</div>
</div>