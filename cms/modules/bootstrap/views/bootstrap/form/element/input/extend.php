<?php if( !empty($prepend) OR !empty($append)): ?>
<div class="<?php if(!empty($prepend)) echo 'input-prepend'; ?> <?php if(!empty($append)) echo 'input-append' ?>">
<?php endif; ?>
	
<?php foreach ($prepend as $string) echo $string; ?>
<?php echo $input; ?>
<?php foreach ($append as $string) echo $string; ?>
<?php if( !empty($prepend) OR !empty($append)): ?>
</div>
<?php endif; ?>