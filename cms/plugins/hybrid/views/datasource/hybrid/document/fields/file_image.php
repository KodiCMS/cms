<div class="control-group">
	<label class="control-label" for="<?php echo $field->name; ?>">
		<?php echo $field->header; ?> 
	</label>
	<div class="controls" id="file-<?php echo $field->name; ?>">
		<div class="row-fluid">
			<?php if( ! empty($value)): ?>
				<div class="well well-small">
					<div class="spoiler-file-<?php echo $field->name; ?>">
						<?php 
						$attrs = array('target' => 'blank');
						if($field->is_image( PUBLICPATH . $value)) $attrs['class'] = 'btn popup fancybox';
						echo HTML::anchor(PUBLIC_URL . $value, UI::icon('file' ) . ' ' . __('View file'), $attrs); 
						?>
						&nbsp;&nbsp;&nbsp;
						<label class="checkbox inline">
						<?php echo Form::checkbox( $field->name, -1, FALSE, array('class' => 'remove-file-checkbox')); ?> <?php echo __('Remove file'); ?>
						</label>
						<hr />
					</div>
					<div class="spoiler-toggle" data-spoiler=".spoiler-file-<?php echo $field->name; ?>">
						
						<i class="icon-chevron-down spoiler-toggle-icon"></i> <?php echo __('Upload new file'); ?>
					</div>
				</div>
				
			<?php endif; ?>
			
			
			
			<div class="upload-new-cont <?php if( ! empty($value)): ?>spoiler<?php endif; ?> spoiler-file-<?php echo $field->name; ?>">
				<div class="span4">
					<?php if( ! empty($value)) echo Form::hidden( $field->name, $value); ?>
					<?php echo Form::file( $field->name, array(
						'id' => $field->name
					) ); ?>
					
					<span class="help-block">
						<?php echo __('Max file size: :size', array(
						':size' => Text::bytes($field->max_size)
						)); ?>
					</span>
					
				</div>
				<div class="span7 input-append">
					<?php echo Form::input( $field->name . '_url', NULL, array(
						'id' => $field->name . '_url', 'placeholder' => __('Or paste URL to file'),
						'class' => 'input-block-level input-filemanager'
					) ); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		var cont = $('#file-<?php echo $field->name; ?>');
		
		$('.remove-file-checkbox', cont).on('change', function() {
			if($(this).is(':checked')) {
				$('.upload-new-cont input', cont).attr('disabled', 'disabled');
				$('.spoiler-toggle,hr', cont).hide();
			} else {
				$('.spoiler-toggle,hr', cont).show();
				$('.upload-new-cont input', cont).removeAttr('disabled');
			}
		})
	})
</script>