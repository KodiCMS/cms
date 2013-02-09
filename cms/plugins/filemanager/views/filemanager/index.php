<script>
	var FILEMANAGER_PATH = '<?php echo $path; ?>';
</script>

<div class="widget widget-nopad">

	<div class="widget-header">
		<?php echo UI::button(__('Create folder'), array(
			'icon' => UI::icon( 'plus' ), 'class' => 'btn', 'id' => 'create-folder'
		)); ?>

		<?php echo Form::file('file', array(
			'id' => 'upload_file', 'multiple', 'class' => 'pull-right'
		)); ?>
	</div>

	<div id="filemanager-container" class="widget-content">

		<div id="filemanager-list">
			<table class=" table table-striped table-hover">
				<colgroup>
					<col />
					<col width="130px" />
					<col width="90px" />
					<col width="120px" />
					<col width="90px" />
				</colgroup>
				<thead>
					<tr>
						<th><?php echo __('File name'); ?></th>
						<th><?php echo __('Modified'); ?></th>
						<th><?php echo __('File size'); ?></th>
						<th><?php echo __('Permissions'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($path)): ?>
					<tr>
						<td colspan="5"><?php echo UI::icon('folder-open'); ?> <a href="<?php echo URL::site('filemanager/'.(!empty($path) ? substr($path, 0, strrpos($path, '/')): '')); ?>"><?php echo __('Level up'); ?></a></td>
					</tr>
					<?php endif; ?>
					<?php foreach ($filesystem as $file): ?>
					<?php if(!$file->isDir() OR $file->isDot()) continue; ?>
					<?php echo View::factory('filemanager/item', array(
						'icon' => 'folder-close', 'file' => $file
					)); ?>
					<?php endforeach; ?>
					<?php foreach ($filesystem as $file): ?>
					<?php if($file->isDir() OR $file->isDot()) continue; ?>
					<?php echo View::factory('filemanager/item', array(
						'icon' => 'file', 'file' => $file
					)); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>