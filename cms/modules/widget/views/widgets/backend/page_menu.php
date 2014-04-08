<div class="widget-content">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Select::factory(array(
				'name' => 'page_id', 'options' => $select
			))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
			->selected($widget->page_id)
			->label(__('Page'))
		));
		
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Checkbox::factory(array(
				'name' => 'match_all_paths', 'value' => 1
			))
			->checked($widget->match_all_paths == 1)
			->label(__('Match All Pages within Given Deepness'))
		));
	?>
	
	<hr />
	
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Checkbox::factory(array(
				'name' => 'include_hidden', 'value' => 1
			))
			->checked($widget->include_hidden == 1)
			->label(__('Include hidden pages'))
		));
	?>
</div>
<div class="widget-header">
	<h4><?php echo __('Exclude pages'); ?></h4>
</div>
<div class="widget-content">
	<table class="table table-striped">
		<colgroup>
			<col width="30px" />
			<col />
		</colgroup>
		<tbody>
			<?php foreach($pages as $page): ?>
			<tr>
				<?php if($page['id'] > 1): ?>
				<td>
					<?php echo Form::checkbox('exclude[]', $page['id'], in_array($page['id'], $widget->exclude), array('id' => 'page'.$page['id'])); ?>
				</td>
				<th><label for="page<?php echo $page['id']; ?>"><?php echo str_repeat('&nbsp;', $page['level'] * 10) . $page['title']; ?></th>
				<?php else: ?>
				<td></td>
				<th><h4><?php echo $page['title']; ?></h4></th>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>