<?php defined('SYSPATH') OR die('No direct script access!');?>
<?php if($breadcrumbs->count()):?>
<p class="breadcrumbs">
<?php $i = 0; foreach($breadcrumbs as $breadcrumb):?>
<?php if($i > 0):?>&raquo;<?endif?>
<?php if($set_urls && ! empty($breadcrumb->url)):?>
<a href="<?=$breadcrumb->url?>" class="breadcrumb<?php if($breadcrumb->active):?><?= " ".$active_class?><?endif?>">
<?endif?>
<span class="breadcrumb<?php if($breadcrumb->active):?><?= " ".$active_class?><?endif?>"><?=$breadcrumb->name?></span>
<?php if($set_urls && ! empty($breadcrumb->url)):?>
</a>
<?endif?>
<?php $i++; endforeach; ?>
</p>
<?endif?>