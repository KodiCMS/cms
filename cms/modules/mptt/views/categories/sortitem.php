<li class="dd-item" data-id="<?php echo $cat['id']; ?>">
	<div class="dd-handle">
		<?php echo UI::icon('file'); ?>
		<span class="title"><?php echo $cat['name']; ?></span>
	</div>
	
	<?php echo $childs; ?>
</li>