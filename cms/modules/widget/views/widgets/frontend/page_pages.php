<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>
	
<?php foreach ($pages as $page): ?>
<div class="media">
    <a class="pull-left" href="<?php echo $page->url(); ?>">
        <img class="media-object" data-src="holder.js/64x64">
    </a>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $page->link(); ?></h4>
        <p class="info">Posted by <?php echo $page->author(); ?> on <?php echo $page->date(); ?> 
        </p>
    </div>
</div>
<hr />
<?php endforeach; ?>