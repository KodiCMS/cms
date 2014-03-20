<br />
<label><?php echo __('Document page'); ?></label>
<?php

$pages = Model_Page_Sitemap::get(TRUE)->find($page->id)->children();
$select = array(__('--- none ---'));
foreach($pages->flatten() as $page)
{
	$uri = !empty($page['uri']) ? $page['uri'] : '/';
	$select[$page['id']] = $page['title'] . ' (' . $uri . ')';
}


echo Form::select('behavior[item_page_id]', $select, Arr::get($settings, 'item_page_id'), array(
	'class' => 'span12'
));
?>
<script>
	cms.ui.init('select2')
</script>