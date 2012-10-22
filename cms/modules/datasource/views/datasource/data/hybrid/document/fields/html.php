<script>
	jQuery(function(){
		cms.filters.switchOn( '<?php echo $field->name; ?>', '<?php echo Setting::get( 'default_filter_id'); ?>');
	});
</script>
<?php
echo Form::textarea( $field->name, $value, array(
	'class' => 'input-plarge', 'id' => $field->name
) );
?>