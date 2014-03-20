<script>
$(function(){
	$('body').on('click', '#refresh-api-key', function() {
		Api.post('api.refresh', {key: $('#api-key').text()}, function(response) {
			if(response.response)
				$('#api-key').text(response.response);
		});
		
		return false;
	});
});
</script>

<div class="widget-header spoiler-toggle" data-spoiler=".api-settings" data-icon="beaker">
	<h3><?php echo __('API'); ?></h3>
</div>
<div class="widget-content spoiler api-settings">
	<div class="lead">
		<?php echo __( 'KodiCMS API key'); ?>: <span id="api-key"><?php echo Config::get('api', 'key'); ?></span>
		
		<?php if( ACL::check('system.api.refresh')): ?>
		<?php echo HTML::anchor('#', UI::icon('refresh') . ' ' . __( 'Change key' ), array(
			'class' => 'btn btn-primary', 'id' => 'refresh-api-key'
		)); ?>
		<?php endif; ?>
	</div>
	<hr />
	<div class="control-group">
		<label class="control-label"><?php echo __( 'API enable' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'setting[api][mode]', Form::choises(), Config::get('api', 'mode')); ?>
		</div>
	</div>
</div>