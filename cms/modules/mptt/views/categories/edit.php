<script type="text/javascript">
	var CATEGORY_ID = <?php echo (int) $category->id; ?>;
</script>

<div id="pageEdit">
	<?php echo Form::open(Route::url('backend', array('controller' => 'categories', 'action' => $action, 'id' => $action == 'add' ? $parent_id : $category->id)), array(
		'class' => Bootstrap_Form::HORIZONTAL, 'method' => Request::POST
	)); ?>
		<?php echo Form::hidden('token', Security::token()); ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<div id="pageEdit" class="box span12">
					<div class="widget ">
						<div class="widget-title">

							<div class="control-group">
								<?php echo $category->label('name', array('class' => 'control-label title')); ?>
								<div class="controls">
									<?php echo $category->field('name', array(
										'class' => 'input-title span12 slug-generator',
										'prefix' => 'category'
									)); ?>
								</div>
							</div>
						</div>
						
						<div class="widget-content-bg ">
							<div class="control-group">
								<?php echo $category->label('path_part', array('class' => 'control-label')); ?>
								<div class="controls">
									<?php echo $category->field('path_part', array(
										'class' => 'span12 slug',
										'prefix' => 'category'
									)); ?>
								</div>
							</div>
						</div>
						<div class="form-actions widget-footer">
							<?php echo UI::actions($page_name); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php echo Form::close(); ?>
</div>