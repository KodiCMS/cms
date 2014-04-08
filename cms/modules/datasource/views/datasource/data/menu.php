<?php if(!empty($tree)): ?>
<div class="widget">
	<?php foreach ($tree as $section => $data): ?>
	<div class="widget-header">
	<h4><?php echo __(ucfirst($section)); ?></h4>
	</div>
	<ul class="list-group unstyled">
	<?php foreach ($data as $id => $name): ?>
		<?php echo recurse_menu($id, $name, $ds_id, $section); ?>
	<?php endforeach; ?>
	</ul>
	<?php endforeach; ?>
	
	<div class="widget-footer">
		<div class="btn-group">
			<?php echo UI::button(__('Create section'), array(
				'href' => '#', 'class' => 'btn dropdown-toggle btn-success',
				'icon' => UI::icon( 'plus icon-white' ), 'data-toggle' => 'dropdown'
			)); ?>

			<ul class="dropdown-menu">
			<?php foreach (Datasource_Data_Manager::types() as $type => $title): ?>
				<?php if(ACL::check($ds_type.'.section.create')): ?>
				<li><?php echo HTML::anchor(Route::get('datasources')->uri(array(
						'controller' => 'section',
						'directory' => 'datasources',
						'action' => 'create',
						'id' => $type
					)), $title); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>

<?php
	function recurse_menu($id, $name, $ds_id, $section)
	{
		if(!ACL::check($section.$ds_id.'.section.view')) return;

		$result = '';
		$selected = ($id == $ds_id) ? 'active' : '';

		$title = $name['name'];
		$result .= '<li class="list-group-item '.$selected.'">';
		$result .= HTML::anchor(Route::get('datasources')->uri(array(
			'controller' => 'data',
			'directory' => 'datasources',
		)) . URL::query(array('ds_id' => $id), FALSE), $title, array('class' => 'list-group-item-heading'));

		$result .= '<div class="btn-group pull-right">';
		if(ACL::check($section.$ds_id.'.section.edit'))
		{
			$result .= UI::button(NULL, array(
				'href' => Route::get('datasources')->uri(array(
					'controller' => 'section',
					'directory' => 'datasources',
					'action' => 'edit',
					'id' => $id
				)),
				'icon' => UI::icon( 'wrench' ),
				'class' => 'btn btn-mini'
			));
		}
		if(ACL::check($section.$ds_id.'.section.edit'))
		{
			$result .= UI::button(NULL, array(
				'href' => Route::get('datasources')->uri(array(
					'controller' => 'section',
					'directory' => 'datasources',
					'action' => 'remove',
					'id' => $id
				)),
				'icon' => UI::icon( 'trash icon-white' ),
				'class' => 'btn btn-danger btn-confirm btn-mini'
			));
		}
		$result .= '</div>';
		
		if(!empty($name['description']))
			$result .= '<p class="muted list-group-item-text">'.$name['description'].'</p>';

		$result .= '</li>';
		
		return $result;
	}

?>