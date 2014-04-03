<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>
	
<h3><?php echo __('Total pages found :total', array(':total' => $total_found)); ?></h3>

<?php foreach ($results as $page): ?>
<div class="media">
    <a class="pull-left" href="<?php echo $page->url(); ?>">
        <img class="media-object" data-src="holder.js/64x64">
    </a>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $page->link(); ?></h4>
    </div>
</div>
<hr />
<?php endforeach; ?>