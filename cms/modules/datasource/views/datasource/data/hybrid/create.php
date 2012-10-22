<?php echo UI::page_header(__( 'Create datasource hybrid' )); ?>


<?php echo Form::open(Request::current()->uri(), array(
	'class' => 'form-horizontal'
)); ?>
	<div class="well">
		<fieldset>
			<legend><?php echo __('General Information'); ?></legend>

			<div class="control-group">
				<label class="control-label" for="ds_key"><?php echo __('Datasource Key'); ?></label>
				<div class="controls">
					<?php
					echo Form::input( 'ds_key', NULL, array(
						'class' => 'input-xlarge', 'id' => 'ds_key'
					) );
					?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="ds_name"><?php echo __('Datasource Header'); ?></label>
				<div class="controls">
					<?php
					echo Form::input( 'ds_name', NULL, array(
						'class' => 'input-xlarge', 'id' => 'ds_name'
					) );
					?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="ds_description"><?php echo __('Datasource Description'); ?></label>
				<div class="controls">
					<?php
					echo Form::textarea( 'ds_description', NULL, array(
						'class' => 'input-xlarge', 'id' => 'ds_description'
					) );
					?>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="well">
		<fieldset>
			<legend><?php echo __('Properties'); ?></legend>

			<div class="control-group">
				<label class="control-label" for="ds_parent"><?php echo __('Datasource parent'); ?></label>
				<div class="controls">
					<?php
					echo Form::select( 'ds_parent', $options, NULL, array(
						'class' => 'input-xlarge', 'id' => 'ds_parent'
					) );
					?>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="form-actions">
		<?php echo UI::button( __('Create hybrid'), array(
			'icon' => UI::icon( 'plus')
		)); ?>
	</div>
</form>

<?php echo Form::close(); ?>