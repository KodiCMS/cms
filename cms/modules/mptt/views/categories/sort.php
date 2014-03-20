<?php

$menu = array(); $ref = array();

foreach( $categories as $category ) 
{
	$category = $category->as_array();
    $category['children'] = array();
    if( isset( $ref[ $category['pid'] ] ) ) 
	{
        $ref[ $category['pid'] ]['children'][ $category['id'] ] = $category;
        $ref[ $category['id'] ] =& $ref[ $category['pid'] ]['children'][ $category['id'] ];
    } 
	else 
	{
        $menu[ $category['id'] ] = $category;
        $ref[ $category['id'] ] =& $menu[ $category['id'] ];
    }
}
?>

 <div class="sort-pages">
	<div class="dd" id="nestable">
		<ul class="dd-list unstyled">
			<?php foreach($menu as $category): ?>
			<li class="dd-item" data-id="<?php echo $category['id']; ?>">
				<div class="dd-handle">
					<?php echo UI::icon('folder-open'); ?>
					<?php echo $category['name']; ?>
				</div>

				<?php 
				if(!empty($category['children']))
				{
					echo recurse_sort_categories($category['children']);
				}
				 ?>
				<?php endforeach; ?>

			</li>
		</ul>
	</div>
</div>
<?php
function recurse_sort_categories(array $childs) {
	$data = '';
	if(empty($childs)) return $data;
	
	$data = '<ul class="dd-list unstyled">';
	foreach ($childs as $cat)
	{
		$data .= (string) View::factory('categories/sortitem', array(
			'cat' => $cat,
			'childs' => !empty($cat['children']) ? recurse_sort_categories($cat['children']) : ''
		));
	}
	
	$data .= '</ul>';
	
	return $data;
}
?>