<script>
	var BLOCKS = <?php echo json_encode($blocks); ?>;
</script>
<div class="widget">
	<?php echo Form::open(Request::current()->uri()); ?>
	<div class="widget-content ">
		<h3>&larr; <?php echo HTML::anchor(Route::get('backend')->uri(array(
				'controller' => 'widgets', 
				'action' => 'edit',
				'id' => $widget->id)), $widget->name); ?></h3>
		<hr />
		<table class="table table-striped">
			<colgroup>
				<col width="300px" />
				<col width="100px" />
				<col />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Layout block'); ?></th>
					<th><?php echo __('Widget weight'); ?></th>
					<th><?php echo __('Page'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php echo recurse_pages($pages, 0, $blocks, $page_widgets, $pages_widgets); ?>
			</tbody>
		</table>
	</div>
	<div class="widget-content ">
		<div class="input-prepend input-append">
			<?php echo Form::input('select_for_all', NULL, array('class' => 'inline')); ?>
			<?php echo UI::button( __('Select for all pages'), array(
				'icon' => UI::icon('sitemap'), 'class' => 'btn inline', 'id' => 'select_for_all'
			)); ?>
		</div>
	</div>
	<div class="widget-footer form-actions">
		<?php echo UI::button( __('Save locations'), array(
			'icon' => UI::icon( 'ok'), 'class' => 'btn btn-large'
		)); ?>
	</div>
	<?php echo Form::close(); ?>
</div>
<?php 
function recurse_pages( $pages, $spaces = 0, $blocks = array(), $page_widgets = array(), $pages_widgets = array() ) 
{
	$data = '';
	foreach ($pages as $page)
	{
		// Выбираем из всех блоков, для шаблона текущей страницы
		$current_page_blocks = isset($blocks[$page['layout_file']]) 
				? $blocks[$page['layout_file']] 
				: array(-1 => __('--- none ---'));
		
		// Исключаем из списка блоки, занятые другими виджетами
//		if(!empty($pages_widgets[$page['id']]) AND is_array($current_page_blocks))
//		{
//			$current_page_blocks = array_diff($current_page_blocks, $pages_widgets[$page['id']]);
//		}

		// Блок
		$current_block = Arr::path($page_widgets, $page['id'].'.0');
		
		$current_position = Arr::path($page_widgets, $page['id'].'.1');
		
		$data .= '<tr data-id="'.$page['id'].'" data-parent-id="'.$page['parent_id'].'">';
		$data .= '<td>';
		$data .= Form::select('blocks[' . $page['id'] . '][name]', $current_page_blocks, $current_block, array('class' => 'blocks') );
		if(!empty($page['childs']))
		{
			$data .= "&nbsp;" . Form::button(NULL, UI::icon('level-down'), array(
				'class' => 'set_to_inner_pages btn btn-mini',
				'title' => __('Select to child pages')
			) );
		}
		$data .= '</td><td>';
		$data .= Form::input('blocks[' . $page['id'] . '][position]', (int) $current_position, array('maxlength' => 4, 'size' => 4, 'class' => 'input-mini text-right widget-position') );
		$data .= '</td>';
		
		if ( Acl::check( 'page.edit'))
		{
			$data .= '<th>' . str_repeat("-&nbsp;", $spaces) . HTML::anchor(Route::get('backend')->uri(array(
				'controller' => 'page',
				'action' => 'edit',
				'id' => $page['id']
			)), $page['title']) . '</th>';
		}
		else
		{
			$data .= '<th>' . str_repeat("-&nbsp;", $spaces) . $page['title'] . '</th>';
		}
		
		$data .= '</tr>';
		
		if(!empty($page['childs']))
		{
			$data .= recurse_pages($page['childs'], $spaces + 5, $blocks, $page_widgets, $pages_widgets);
		}
	}
	return $data;
} 
?>