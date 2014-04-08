<div class="widget-header">
	<h3><?php echo __('General settings'); ?></h3>
</div>
<div class="widget-content">
	<div class="control-group">
		<?php echo Form::label('setting_project_id', __('Project ID'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[project_id]', $plugin->get('project_id', 00000), array(
				'id' => 'setting_project_id', 'class' => '', 'maxlength' => 10, 'size' => 10
			)); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo Form::label('setting_project_host', __('Project address (*.reformal.ru)'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[project_host]', $plugin->get('project_host', '.reformal.ru'), array(
				'id' => 'setting_project_host', 'class' => '', 'maxlength' => 100, 'size' => 100
			)); ?>
		</div>
	</div>
</div>

<div class="widget-header">
	<h3><?php echo __('Label settings'); ?></h3>
</div>
<div class="widget-content">
	<div class="control-group">
		<?php echo Form::label('setting_tab_alignment', __('Label align'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::select('setting[tab_alignment]', array(
					'left' => __('Left'),
					'right' => __('Right')
				), $plugin->get('tab_alignment', 'right'), array('id' => 'setting_tab_alignment')); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo Form::label('setting_tab_bg_color', __('Label color'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::input('setting[tab_bg_color]', $plugin->get('tab_bg_color', '#F08200'), array(
				'id' => 'setting_tab_bg_color', 'class' => '', 'maxlength' => 7, 'size' => 7
			)); ?>
		</div>
	</div>
</div>