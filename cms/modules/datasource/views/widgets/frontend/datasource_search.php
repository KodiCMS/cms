<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>
	
<h3><?php echo __('Total results found :total', array(':total' => $total_found)); ?></h3>

<?php foreach ($results as $item): ?>
<div class="media">
	<div class="media-body">
		<h4 class="media-heading"><?php echo HTML::anchor($item['href'], $item['title']); ?></h4>
		<?php if(!empty($item['annotation'])): ?>
		<p><?php echo $item['annotation']; ?></p>
		<?php endif; ?>
	</div>
</div>
<hr />
<?php endforeach; ?>