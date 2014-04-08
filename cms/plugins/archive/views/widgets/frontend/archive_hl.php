<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>
	
<?php foreach ($pages as $archive): ?>
<div class="media">
    <a class="pull-left" href="<?php echo $archive->url(); ?>">
        <img class="media-object" data-src="holder.js/64x64">
    </a>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $archive->link(); ?></h4>
        <p class="info">Posted by <?php echo $archive->author(); ?> on <?php echo $archive->date(); ?> 
        </p>
    </div>
</div>
<hr />
<?php endforeach; ?>