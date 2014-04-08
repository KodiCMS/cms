<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>

<?php if(!empty($crumbs)): ?>
<ul class="breadcrumb">
	<?php foreach($crumbs as $page): ?>
	<?php if($crumbs->is_last()): ?>
	<li class="active"><?php echo $page->name; ?></li>
	<?php else: ?>
	<li><?php echo $page->link(); ?></li>
	<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>