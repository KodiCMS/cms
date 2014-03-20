<script>
	var DS_ID = '<?php echo $ds->id(); ?>';
</script>

<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>

	<?php echo Form::hidden('ds_id', $ds->id()); ?>
	
	<div class="widget-header">
		<h3><?php echo __( 'Add field' ); ?></h3>
	</div>

	<div class="widget-content" id="filed-type">
		<div class="control-group">
			<label class="control-label title" for="header"><?php echo __('Field header'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'header', Arr::get($post_data, 'header'), array(
					'class' => 'slug-generator input-title input-block-level', 'id' => 'header', 'data-separator' => '_'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name"><?php echo __('Field key'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'name', Arr::get($post_data, 'name'), array(
					'class' => 'input-xlarge slug', 'id' => 'name'
				) ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="field-type-select"><?php echo __('Field type'); ?></label>
			<div class="controls">
				<?php echo Form::select( 'type', DataSource_Hybrid_Field::types(), Arr::get($post_data, 'type'), array(
					'id' => 'field-type-select'
				)); ?>
			</div>
		</div>
		
		
		<div id="field-options">
			<?php foreach (DataSource_Hybrid_Field::types() as $type => $title) 
			{
				if(is_array($title))
				{
					foreach ($title as $type => $title)
					{
						try {
							echo View::factory('datasource/hybrid/field/add/' . $type, array(
								'sections' => $sections, 'post_data' => $post_data, 'title' => $title
							));
						} catch (Exception $exc) {}
					}
				}
				else
				{
					try {
						echo View::factory('datasource/hybrid/field/add/' . $type, array(
							'sections' => $sections, 'post_data' => $post_data, 'title' => $title
						));
					} catch (Exception $exc) {}
				}
			} ?>
		</div>
		
		<hr />
		
		<div class="control-group">
			<label class="control-label" for="position"><?php echo __('Field position'); ?></label>
			<div class="controls">
				<?php echo Form::input( 'position', Arr::get($post_data, 'position', 500), array(
					'id' => 'position',
					'class' => 'input-mini'
				)); ?>
			</div>
		</div>
	</div>
	<div class="widget-footer form-actions">
		<?php echo UI::button( __('Add field'), array(
			'icon' => UI::icon( 'plus'), 'class' => 'btn btn-large'
		)); ?>
	</div>
<?php echo Form::close(); ?>
</div>