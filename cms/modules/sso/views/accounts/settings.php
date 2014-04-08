<div class="widget-header spoiler-toggle" data-spoiler=".social-accounts-settings" data-hash="social-accounts-settings" data-icon="comments">
	<h3 id="social-accounts-settings"><?php echo __('Social accounts settings'); ?></h3>
</div>
<div class="widget-content spoiler social-accounts-settings">
	
	<div class="control-group">
		<label class="control-label"><?php echo __( 'Enable registration' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'setting[oauth][register]', Form::choises(), Config::get('oauth', 'register' ) ); ?>
		</div>
	</div>

	<?php foreach ($oauth as $provider => $data): ?>
	<div class="well">
		<h4><?php echo UI::icon($provider.'-sign'); ?> <?php echo Arr::path($params, $provider.'.name'); ?> <?php if(Arr::path($params, $provider.'.create_link')): ?>(<?php echo HTML::anchor(Arr::path($params, $provider.'.create_link'), NULL, array(
			'target' => 'blank'
		)); ?>)<?php endif; ?></h4>
		<hr />
		<?php foreach ($data as $key => $value): ?>
		<div class="control-group">
			<label class="control-label"><?php echo strtoupper($key); ?></label>
			<div class="controls">
				<?php echo Form::input( 'setting[oauth][accounts]['.$provider.']['.$key.']', $value, array(
					'class' => 'input-xxlarge'
				)); ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>