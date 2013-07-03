 <div class="sort-pages">
	<?php foreach($pages as $page): ?>
	<ul class="dd-list unstyled">
		<li class="dd-item" data-id="<?php echo $page['id']; ?>">
			<div class="dd-root">
				<?php echo UI::icon('folder-open'); ?>
				<?php echo $page['title']; ?>
			</div>
			
			<div class="dd" id="nestable">
			<?php 
			if(!empty($page['childs']))
			{
				echo recurse_sort_pages($page['childs']);
			}
			 ?>
			<?php endforeach; ?>
			</div>
		</li>
	</ul>
</div>
<?php
function recurse_sort_pages(array $childs) {
	$data = '';
	if(empty($childs)) return $data;
	
	$data = '<ul class="dd-list unstyled">';
	foreach ($childs as $page)
	{
		$data .= (string) View::factory('page/sortitem', array(
			'page' => $page,
			'childs' => !empty($page['childs']) ? recurse_sort_pages($page['childs']) : ''
		));
	}
	
	$data .= '</ul>';
	
	return $data;
}
?>