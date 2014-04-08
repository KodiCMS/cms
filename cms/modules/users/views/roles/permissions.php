<div class="widget-header widget-section">
	<h2><?php echo __('Section permissions'); ?></h2>
</div>

<?php foreach($permissions as $title => $actions):?>

<div class="widget-header">
	<h3><?php echo __(ucfirst($title)); ?></h3>
</div>
<div class="widget-content widget-nopad">
	<table class="table table-hover" id="permissions-list">
		<colgroup>
			<col width="20px" />
			<col />
		</colgroup>
		<thead class="highlight">
			<tr>
				<th colspan="2">
					<label class="check_all"><?php echo __('Select all'); ?></label>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($actions as $action => $title): ?>
			<tr>
				<td>
					<?php echo Bootstrap_Form_Element_Checkbox::factory(array(
						'name' => 'role[permissions]['.$action.']', 'value' => 1
					))
					->attributes('id', 'permission-'.$action)
					->checked(in_array($action, $role_permissions)); ?>
				</td>
				<th><?php echo Form::label('permission-'.$action, __($title)); ?></th>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endforeach; ?>
	
