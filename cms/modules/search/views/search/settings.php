<div class="widget-header spoiler-toggle" data-spoiler=".search-settings" data-hash="search-settings" data-icon="hdd">
	<h3 id="cache-settings"><?php echo __('Search settings'); ?></h3>
</div>
<div class="widget-content spoiler search-settings">
	<div class="well">
		<?php echo UI::button(__('Update search index'), array(
			'icon' => UI::icon( 'stethoscope' ),
			'id' => 'update-search-index',
			'class' => 'btn btn-warning'
		)); ?>
	</div>
</div>