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