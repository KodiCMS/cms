<script>
	var DS_ID = '<?php echo $ds->ds_id; ?>';
</script>
<?php echo UI::page_header(__( 'Edit hybrid field' )); ?>

<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<div class="well" id="filed-type">
		<div class="control-group">
			<label class="control-label" for="name"><?php echo __('Field key'); ?></label>
			<div class="controls">
				<?php if($field->family === DataSource_Hybrid_Field::TYPE_PRIMITIVE): ?>
				<?php echo Form::input( 'name', Arr::get($post_data, 'name', $field->name), array(
					'class' => 'input-xlarge', 'id' => 'name'
				) ); ?>
				<?php else: ?>
				<?php echo UI::field($field->name); ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="header"><?php echo __('Field header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'header', Arr::get($post_data, 'header', $field->header), array(
					'class' => 'input-xlarge', 'id' => 'header'
				) ); ?>
			</div>
		</div>
		
		
		<div id="field-options">
			<?php 
			try
			{
				echo View::factory('datasource/data/hybrid/field/edit/' . $type, array(
					'field' => $field
				));
			}
			catch(Exception $e) {} ?>
		</div>
		
		<hr />
		
		<div class="control-group">
			<label class="control-label" for="isreq"><?php echo __('Required'); ?></label>
			<div class="controls">
				<div class="checkbox">
					<?php echo Form::checkbox( 'isreq', 1, (Arr::get($post_data, 'isreq', $field->isreq) == 1), array(
						'id' => 'isreq'
					)); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<?php echo UI::actions('hybrid/section/edit/' . $ds->ds_id); ?>
	</div>
<?php echo Form::close(); ?>