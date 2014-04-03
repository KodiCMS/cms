<div class="widget" id="profile">
	<div class="tabbable tabs-left">
		<ul class="nav nav-tabs"></ul>
		<div class="tab-content"></div>
	</div>
	<div class="widget-header widget-header-onlytab">
		<h3><?php echo __('General information'); ?></h3>
	</div>
	<div class="widget-content">
		<div class="profile-header">
			<?php echo HTML::anchor('http://gravatar.com/emails/', $user->gravatar(100, NULL, array('class' => 'profile-avatar img-circle')), array(
				'target' => '_blank',
			)); ?>

			<h2 class="profile-username"><?php echo $user->username; ?> <small><?php echo __('Last login'); ?> <?php echo Date::format($user->last_login); ?></small></h2>
			<?php if(!empty($user->profile->name)): ?><p class="profile-name muted"><?php echo $user->profile->name; ?></p><?php endif; ?>
			
			<div class="clearfix"></div>
		</div>
		
		<div class="profile-toolbar">
			<?php Observer::notify('view_user_profile_toolbar', $user->id); ?>
		</div>
		
		<div class="row-fluid">
			
			<div class="span5">
				<div class="list-group">
					<?php if ( Acl::check( 'users.edit') OR $user->id == AuthUser::getId() ): ?>
					<?php echo HTML::anchor(
      						Route::get('backend')->uri(array(
						        'controller' => 'users',
						        'action' => 'edit',
						        'id' => $user->id
						      )),
						'<i class="icon-user"></i>&nbsp;&nbsp;'.__('Edit profile').'<i class="list-group-chevron icon-chevron-right"></i>',
					        array('class'=>'list-group-item')
					); ?>
					<?php endif; ?>
					<?php if(!empty($user->email)): ?>
					<a href="mailto:<?php echo $user->email; ?>" class="list-group-item">
						<i class="icon-envelope-alt"></i>&nbsp;&nbsp;<?php echo $user->email; ?>
					</a>
					<?php endif; ?>
					<?php Observer::notify('view_user_profile_sidebar_list', $user->id); ?>
				</div>
				<?php Observer::notify('view_user_profile_sidebar', $user->id); ?>
			</div>
			<div class="span7">
				<?php Observer::notify('view_user_profile_information', $user->id); ?>
			</div>
		</div>
		
	</div>
	
	<?php if ( !empty($permissions) AND ACL::check('users.view.permissions') ): ?>
		<div class="widget-header widget-section">
			<h2><?php echo __('Section permissions'); ?></h2>
		</div>
		
		<?php foreach($permissions as $title => $actions): ?>
		<div class="widget-header">
			<h3><?php echo __(ucfirst($title)); ?></h3>
		</div>
		<div class="widget-content">
			
			<table class="table "permissions-list">
				<tbody>
					<?php foreach($actions as $action => $title): ?>
					<tr>
						<td><?php echo __($title); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
