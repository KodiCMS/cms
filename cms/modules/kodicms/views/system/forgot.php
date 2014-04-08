<div id="login-form" class="hero-unit raised outline">
	<div class="outline_inner">
		<h1><?php echo __( 'Forgot password?' ); ?></h1>
		<p class="muted"><?php echo __('Enter your e-mail, which you want to forgot password.'); ?></p>
		<hr />
		<?php echo Form::open( Route::get( 'user' )->uri( array( 'action' => 'forgot' ) ), array( 'method' => 'post' ) ); ?>

		<?php echo Form::hidden( 'token', Security::token() ); ?>

		<div class="control-group">			
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-envelope"></i></span>
				<?php echo Form::input('forgot[email]', NULL, array(
					'class' => 'login-field'
				)); ?>

				<?php echo Form::button('send', __( 'Send password' ), array('class' => 'btn btn-large btn-success')); ?>
			</div>			
		</div>

		<?php Observer::notify( 'admin_login_forgot_form' ); ?>

		<hr />
		<?php echo HTML::anchor(Route::get( 'user' )->uri( array( 
			'action' => 'login'
			)), UI::icon('chevron-left') . ' ' . __( 'Login' ), array(
			'class' => 'btn btn-large'
		)); ?>
		<?php Form::close(); ?>
	</div>
</div>