<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>

<?php foreach($links as $link): ?>
<div class="media">
	<div class="media-body">
		<h4 class="media-heading"><?php echo HTML::anchor($link['href'], $link['title']); ?></h4>
	</div>
</div>
<?php endforeach; ?>