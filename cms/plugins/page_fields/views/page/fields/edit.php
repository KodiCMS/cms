<?php if( ACL::check('page.custom_fields')): ?>
<div class="widget-header  spoiler-toggle" data-spoiler=".spoiler-page-fields" data-hash="page-fields">
	<h4><?php echo __('Page fields'); ?></h4>
</div>

<div class="widget-content  spoiler spoiler-page-fields">
	<?php if(empty($page->id)): ?>
	<h4><?php echo __('Copy fields from'); ?></h4>
	<select name="fields[from_page_id]" class="span12">
		<option value="0"><?php echo __("Don't copy"); ?></option>
		<?php foreach ($pages as $p): ?>
		<option value="<?php echo($p['id']); ?>" <?php echo($p['id'] == $page->parent_id ? ' selected="selected"': ''); ?> ><?php echo str_repeat('- ', $p['level'] * 2).$p['title']; ?></option>
		<?php endforeach; ?>
	</select>
	<?php else: ?>
	<div class="well">
		<div class="span5 select-field-container" style='display:none;'>
			<?php echo Form::hidden('field_id', NULL, array(
				'disabled', 'class' => 'span12', 'id' => 'select-page-field-container'
			)); ?>
		</div>
		
		<?php echo View::factory('page/fields/field', array(
			'field' => ORM::factory( 'page_field')
		)); ?>
		<hr />
		<button id="select-page-field" class="btn"><?php echo __('Show field select'); ?></button>
	</div>
	<?php foreach($fields as $field): ?>
	<?php echo View::factory('page/fields/field', array(
		'field' => $field
	)); ?>
	<?php endforeach; ?>
	<?php endif; ?>
</div>
<?php endif; ?>