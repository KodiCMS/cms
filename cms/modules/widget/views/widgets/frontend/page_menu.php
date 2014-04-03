<?php if(!empty($header)): ?>
<h3><?php echo $header; ?></h3>
<?php endif; ?>

<div class="navbar">
	<?php echo recurse_page_menu( $pages ); ?>
</div>

<?php function recurse_page_menu( $pages ) {
	
	$return = '<ul class="nav">';
	
	foreach($pages as $page)
	{
		$return .= '<li>';
		$return .= HTML::anchor($page['uri'], $page['title'], array(
			'class' =>  $page['is_active'] ? 'current' : ''
		));
		
		if( !empty($page['childs']))
		{
			$return .= recurse_page_menu($page['childs']);
		}
		
		$return .= '</li>';
	}
	
	$return .= '</ul>';
	
	return $return;
}