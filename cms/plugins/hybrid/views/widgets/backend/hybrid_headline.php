<?php echo View::factory('widgets/backend/blocks/section', array(
	'widget' => $widget
)); ?>

<?php if( ! $widget->ds_id ): ?>
<div class="widget-content">
	<div class="alert alert-warning">
		<i class="icon icon-lightbulb"></i> <?php echo __('You need select hybrid section'); ?>
	</div>
</div>
<?php else: ?>
<div class="widget-header">
	<h4><?php echo __('Properties'); ?></h4>
</div>
<div class="widget-content">
	<div class="control-group">
		<label class="control-label" for="doc_uri"><?php echo __('Document page (URI)'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'doc_uri', $widget->doc_uri, array(
				'class' => 'input-xlarge', 'id' => 'doc_uri'
			) ); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="doc_id"><?php echo __('Identificator field'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'doc_id', $widget->doc_id, array(
				'class' => 'input-xlarge', 'id' => 'doc_id'
			) ); ?>
			<span class="help-block">
				<?php echo __('Multiple fields specify separated by commas'); ?>
			</span>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="checkbox"><?php echo Form::checkbox('throw_404', 1, $widget->throw_404); ?> <?php echo __('Generate error 404 when page has no content'); ?></label>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="checkbox"><?php echo Form::checkbox('only_published', 1, $widget->only_published); ?> <?php echo __('Show only published documents'); ?></label>
		</div>
	</div>
</div>

<div class="widget-content ">
	<?php
		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'search_key', 'value' => $widget->search_key
			))
			->label(__('Search key (Ctx)'))
		));
	?>
</div>

<?php echo View::factory('widgets/backend/blocks/fields', array(
	'widget' => $widget
)); ?>

<?php echo View::factory('widgets/backend/blocks/sorting', array(
	'ds_id' => $widget->ds_id,
	'doc_order' => $widget->doc_order
)); ?>

<?php echo View::factory('widgets/backend/blocks/filters', array(
	'widget' => $widget
)); ?>
<?php endif; ?>