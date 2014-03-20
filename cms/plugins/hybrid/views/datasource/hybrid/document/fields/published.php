<div id="doc-published">
	<label class="radio inline">
		<?php echo Form::radio('published', 1, !empty($doc->published)); ?> <?php echo __('Published'); ?>
	</label>

	<label class="radio inline">
		<?php echo Form::radio('published', 0, empty($doc->published)); ?> <?php echo __('Unpublished'); ?>
	</label>
</div>