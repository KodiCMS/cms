<div class="frontend-header">
	<a href="/" class="logo">
		<?php echo HTML::image( ADMIN_RESOURCES . 'images/logo-color.png'); ?>
	</a>
</div>

<div class="page-signin-alt">
	<?php 
	echo Form::open(Route::get('user')->uri(array('action' => 'login')), array(
		'method' => 'post', 'class' => 'panel', 'id' => 'signin-form_id'
	));
	
	echo Form::hidden( 'token', Security::token() ); 
	?>
	
	<?php if( is_array( $install_data)): ?>
	<div class="alert alert-page alert-info alert-dark">
		<h4><?php echo __('KodiCMS successfully installed!'); ?></h4>
		<ul class="list-unstyled">
			<li><?php echo __('Login: :login', array(':login' => Arr::get($install_data, 'username'))); ?></li>
			<li><?php echo __('Password: :password', array(':password' => Arr::get($install_data, 'password_field'))); ?></li>
		</ul>
	</div>
	<?php endif; ?>
	<div class="panel-body">
		<div class="form-group">
			<?php echo Form::input('login[username]', NULL, array(
				'id' => 'username_id', 'class' => 'form-control input-lg', 'placeholder' => __('Username or email')
			)); ?>
		</div>

		<div class="form-group signin-password">
			<?php echo Form::password('login[password]', NULL, array(
				'id' => 'password_id', 'class' => 'form-control input-lg', 'placeholder' => __('Password')
			)); ?>

			<?php echo HTML::anchor(ADMIN_DIR_NAME . '/login/forgot', __('Forgot password?'), array('class' => 'forgot')); ?>
		</div>

		<div class="form-group">
			<label class="checkbox-inline">
				<?php echo Form::checkbox('login[remember]', 'checked', TRUE, array('class' => 'px', 'id' => 'rememder_id')); ?>
				<span class="lbl"><?php echo __('Remember me for :num days', array(':num' => Kohana::$config->load('auth.lifetime') / Date::DAY )); ?></span>
			</label>
		</div>
	</div>
	
	<?php Observer::notify('admin_login_form'); ?>
	
	<div class="panel-footer">
		<?php echo Form::button('sign-in', __('Login'), array(
			'class' => 'btn btn-success btn-lg'
		)); ?>
	</div>

	<?php echo Form::close(); ?>
	
	<?php Observer::notify('admin_login_form_after_button'); ?>
</div>