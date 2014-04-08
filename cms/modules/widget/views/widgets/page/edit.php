<?php if( ACL::check( 'widgets.index')): ?>
<script>
	var LAYOUT_BLOCKS = <?php echo json_encode( $blocks ); ?>;
</script>
<div class="widget-header  spoiler-toggle" data-spoiler=".spoiler-widgets" data-hash="widgets">
	<h4><?php echo __('Widgets'); ?></h4>
</div>

<div class="widget-content  spoiler spoiler-widgets">
	<?php if(empty($page->id)): ?>
	<h4><?php echo __('Copy widgets from'); ?></h4>
	<select name="widgets[from_page_id]" class="span12">
		<option value=""><?php echo __('--- Do not copy ---'); ?></option>
		<?php foreach ($pages as $p): ?>
		<option value="<?php echo($p['id']); ?>" <?php echo($p['id'] == $page->parent_id ? ' selected="selected"': ''); ?> ><?php echo str_repeat('- ', $p['level'] * 2).$p['title']; ?></option>
		<?php endforeach; ?>
	</select>
	<?php else: ?>
	
	<?php if( ACL::check( 'widgets.location')): ?>
	<?php echo HTML::anchor(
		'/api-widget.list/'.$page->id,
		'<i class="icon-plus"></i>'.__( 'Add widget to page' ),
		array(
			'id' => 'addWidgetToPage',
			'class' => 'btn btn-success fancybox.ajax popup'
		)
	); ?>
	<br /><br />
	<?php endif; ?>
	<table class="table table-hover" id="widget-list">
		<colgroup>
			<col />
			<col width="100px" />
			<col width="280px" />
		</colgroup>
		<tbody>
		<?php foreach($widgets as $widget): ?>
		<?php echo View::factory( 'widgets/ajax/row', array(
			'widget' => $widget
		)); ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<?php endif; ?>