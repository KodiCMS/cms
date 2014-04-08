<div class="widget">
	<div class="widget-header">
		<?php echo UI::button(__('Create database backup'), array(
			'icon' => UI::icon('list'), 
			'href' => Route::get('backend')->uri(array(
				'controller' => 'backup',
				'action' => 'database'
			))
		)); ?>

		<?php echo UI::button(__('Create filesystem backup'), array(
			'icon' => UI::icon('file'), 'href' => Route::get('backend')->uri(array(
				'controller' => 'backup',
				'action' => 'filesystem'
			))
		)); ?>
	</div>

	<div id="backup-container" class="widget-content widget-nopad">
		<div id="backups-list">
			<?php echo $files; ?>
		</div>
	</div>
	
	<div class="widget-header">
		<?php echo UI::icon('upload'); ?><h3><?php echo __('Upload backup file'); ?></h3>
	</div>
	<div class="widget-content">
		<?php 
		echo Form::open('api-backup.upload', array(
			'enctype' => 'multipart/form-data',
			'method' => Request::POST,
			'class' => 'dropzone',
		));
		echo Form::hidden('token', Security::token()); 
		?>
		<?php echo Form::close(); ?>
	</div>
</div>