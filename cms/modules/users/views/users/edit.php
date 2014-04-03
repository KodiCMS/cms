<script type="text/javascript">
	var USER_ID = <?php echo (int) $user->id; ?>;
</script>
	
<?php echo Form::open(Route::get('backend')->uri(array('controller' => 'users', 'action' => $action, 'id' => $user->id)), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<div class="widget">
		<?php if($user->loaded()): ?>
		<div class="tabbable tabs-left">
			<ul class="nav nav-tabs"></ul>
			<div class="tab-content"></div>
		</div>
		<?php endif; ?>
		<div class="widget-header">
			<h3><?php echo __('General information'); ?></h3>
		</div>
		<div class="widget-content">
			<div class="row-fluid">
				<div class="span8">
					<div class="control-group">
						<?php echo $user->profile->label('name', array('class' => 'control-label')); ?>
						<div class="controls">
							<?php echo $user->profile->field('name', array(
								'class' => 'input-medium',
								'prefix' => 'profile'
							)); ?>	
						</div>
					</div>
					
					<div class="control-group">
						<?php echo $user->label('email', array('class' => 'control-label')); ?>
						<div class="controls">
							<div class=" input-append">
								<?php echo $user->field('email', array(
									'class' => 'input-medium',
									'prefix' => 'user'
								)); ?>
								<span class="add-on"><?php echo UI::icon('envelope'); ?></span>
							</div>
						</div>
					</div>
					
					<div class="control-group">
						<?php echo $user->label('username', array('class' => 'control-label')); ?>
						<div class="controls">
							<div class="input-append">
								<?php echo $user->field('username', array(
									'class' => 'input-medium',
									'prefix' => 'user'
								)); ?>
								<span class="add-on"><?php echo UI::icon('user'); ?></span>
							</div>
							
							<span class="help-block"><?php echo __('At least :num characters. Must be unique.', array(
								':num' => 3
							)); ?></span>
						</div>
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="control-group">
				<?php echo $user->profile->label('locale', array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo $user->profile->field('locale', array(
						'prefix' => 'profile'
					)); ?>	
				</div>
			</div>
		</div>
		
		<div class="widget-header">
			<h3><?php echo __('Notifications'); ?></h3>
		</div>
		
		<div class="widget-content">
			<div class="control-group">
				<div class="controls form-inline">
					<?php echo $user->profile->field('notice', array(
						'prefix' => 'profile',
					)); ?>	
					<?php echo $user->profile->label('notice', array(
						'class' => 'checkbox'
					)); ?>
				</div>
			</div>
			
			<?php Observer::notify('view_user_edit_notifications', $user->id); ?>
		</div>

		<?php if( ACL::check('users.change_password') OR $user->id == AuthUser::getId() OR !$user->loaded() ): ?>
		<div class="widget-header spoiler-toggle" data-spoiler=".password-spoiler">
			<h3><?php echo __('Password'); ?></h3>
		</div>
		<div class="widget-content spoiler password-spoiler">
			<?php if($action == 'edit'): ?>
			<div class="alert alert-warning">
				<i class="icon icon-lightbulb"></i> <?php echo __('Leave password blank for it to remain unchanged.'); ?>
			</div>
			<?php endif; ?>
			<?php
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Password::factory(array(
					'name' => 'user[password]'
				), array('id' => 'userEditPasswordField', 'autocomplete' => 'off', 
					'placeholder' => __('Password')))
				->label(__('Password'))
				->append(Bootstrap_Form_Element_Input::add_on(UI::icon('lock')))
				->help_text(__('At least :num characters.', array(
					':num' => Kohana::$config->load('auth')->get( 'password_length' )
				)))
			));
			
			echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Password::factory(array(
					'name' => 'user[password_confirm]'
				), array('id' => 'userEditPasswordConfirmField', 
					'autocomplete' => 'off', 'placeholder' => __('Confirm Password')))
				->label(__('Confirm Password'))
			)); 
			?>
			<?php Observer::notify('view_user_edit_password', $user->id); ?>
		</div>
		<?php endif; ?>

		<?php if (Acl::check( 'users.change_roles') AND ($user->id === NULL OR $user->id > 1)): ?>
		<div class="widget-header">
			<h3><?php echo __('Roles'); ?></h3>
		</div>
		<div class="widget-content">
			<div class="row-fluid">
			<?php 
				echo Form::hidden('user_permission', (int) $user->id, array(
					'class' => 'span12'
				));
				
				echo Bootstrap_Form_Helper_Help::factory(array(
					'text' => __('Roles restrict user privileges and turn parts of the administrative interface on or off.')
				)); 
			?>
			</div>
		</div>
		<?php endif; ?>
		
		<?php Observer::notify('view_user_edit_plugins', $user); ?>
		
		<div class="form-actions widget-footer">
			<?php echo UI::actions($page_name); ?>
		</div>
	
	</div>
<?php Form::close(); ?>