<?php if(count($providers) > 0): ?>
<hr />
<h4><?php echo __('Sign-in with your social network'); ?></h4>
<div class="btn-group">
	<?php foreach ($providers as $provider => $data): ?>
	<?php echo UI::button(Arr::path($params, $provider.'.name'), array(
		'icon' => UI::icon($provider),
		'class' => 'btn btn-inverse',
		'href' => Route::get('accounts-auth')->uri(array(
			
			'directory' => 'oauth', 
			'controller' => $provider, 
			'action' => 'login'))
	)) ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>