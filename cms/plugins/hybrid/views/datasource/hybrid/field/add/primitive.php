<fieldset id="f-<?php echo DataSource_Hybrid_Field::TYPE_PRIMITIVE; ?>" disabled="disabled">
	<hr />
	<div class="control-group">
		<label class="control-label" for="primitive_type"><?php echo __( 'Primitive type' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'type', DataSource_Hybrid_Field_Primitive::types(), Arr::get($post_data, 'type') ); ?>
		</div>
	</div>
</fieldset>