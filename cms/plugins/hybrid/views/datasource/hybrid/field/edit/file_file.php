<?php 
$config = Kohana::$config->load('mimes')->as_array(); 
$mimes = array();
foreach($config as $ext => $mime)
{
	$mimes[$ext] = $ext;
}
?>
<div class="widget-content ">
	<div class="control-group">
		<label class="control-label"><?php echo __( 'Allowed file types' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'types[]', $mimes, (array) $field->types, array('class' => 'input-block-level', 'id' => 'allowed_types')); ?>
			
			<span class="flags" data-array="true">
				<span class="label" data-value="bmp,gif,jpg,png,tif"><?php echo __('Image types'); ?></span>
				<span class="label" data-value="doc,docx,xls,txt,pdf"><?php echo __('Document types'); ?></span>
				<span class="label" data-value="rar,zip,tar,gz,7z"><?php echo __('Archive types'); ?></span>
				<span class="label" data-value="mp3,wav"><?php echo __('Audio types'); ?></span>
			</span>
		</div>
	</div>

	<hr />
	<div class="control-group">
		<label class="control-label"><?php echo __( 'Max file size' ); ?></label>
		<div class="controls">
			<?php echo Form::input('max_size', $field->max_size, array('class' => 'input-small', 'id' => 'max_size')); ?> (<?php echo Text::bytes($field->max_size); ?>)
			<span class="flags">
				<span class="label" data-value="<?php echo NUM::bytes('100K'); ?>">100k</span>
				<span class="label" data-value="<?php echo NUM::bytes('1MiB'); ?>">1Mib</span>
				<span class="label" data-value="<?php echo NUM::bytes('5MiB'); ?>">5Mib</span>
				<span class="label" data-value="<?php echo NUM::bytes('10MiB'); ?>">10Mib</span>
			</span>
		</div>
	</div>
</div>