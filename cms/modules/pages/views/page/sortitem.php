<li class="dd-item" data-id="<?php echo $page['id']; ?>">
	<div class="dd-handle">
		<?php echo UI::icon('file'); ?>
		<span class="title"><?php echo $page['title']; ?></span>

		<?php if( !empty($page['behavior_id']) ): ?> 
		&nbsp;&nbsp;<?php echo UI::label(Inflector::humanize($page['behavior_id']), 'default'); ?>
		<?php endif; ?>
	</div>
	
	<?php echo $childs; ?>
</li>