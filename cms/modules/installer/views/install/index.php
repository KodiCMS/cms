<h1><?php echo $title; ?></h1>
<?php echo Form::open('install/go', array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>
<div id="wizard">
	<h1><?php echo __( 'Language' ); ?></h1>
	<div>
		<div class="widget">
			<div class="widget-content">
				<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'locale', 'options' => I18n::available_langs()
					))
					->attributes('id', 'current-lang')
					->selected(Arr::get($_GET, 'lang', I18n::lang()))
					->label(__('Current language'))
				)); ?>
			</div>
		</div>
	</div>
	<h1><?php echo __( 'Environment Tests' ); ?></h1>
	<div>
		<div class="widget">
			<?php echo $env_test; ?>
		</div>
	</div>
    <h1><?php echo __( 'Database information' ); ?></h1>
    <div>
		<div class="widget">
			<div id="install-page" class="widget-content">				
				<p class="lead"><?php echo __('Below you should enter your database connection details. If you’re not sure about these, contact your host.'); ?>
				<hr />
				
				<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'install[db_driver]', 'options' => array(
							'mysql' => __('MySQL'),
							'pdo' => __('PDO')
						)
					))
					->selected(Arr::get( $data, 'db_driver' ))
					->label(__('Database driver'))
				)); ?>

				<div class="control-group">
					<label class="control-label" for="installDBServerField"><?php echo __( 'Database server' ); ?></label>
					<div class="controls inline">
						<?php echo Form::input( 'install[db_server]', Arr::get( $data, 'db_server' ), array(
							'class' => 'span3', 'id' => 'installDBServerField'
						) ); ?>

						<?php echo Form::input( 'install[db_port]', Arr::get( $data, 'db_port' ), array(
							'class' => 'span1'
						) ); ?>

						<?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="installDBUserField"><?php echo __( 'Database user' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[db_user]', Arr::get( $data, 'db_user' ), array(
							'class' => 'input-xlarge', 'id' => 'installDBUserField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="installDBPasswordField"><?php echo __( 'Database password' ); ?></label>
					<div class="controls">
						<?php echo Form::password( 'install[db_password]', Arr::get( $data, 'db_password' ), array(
							'class' => 'input-xlarge', 'id' => 'installDBPasswordField'
						) ); ?>

						<p class="help-block"><?php echo __( 'If there is no database password, leave it blank.' ); ?></p>
					</div>
				</div>

				<div class="control-group well well-small">
					<label class="control-label" for="installDBNameField"><?php echo __( 'Database name' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[db_name]', Arr::get( $data, 'db_name' ), array(
							'class' => 'input-xlarge', 'id' => 'installDBNameField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
						<p class="help-block"><?php echo __( 'You have to create a database manually and enter its name here.' ); ?></p>
						
						<hr />
						<label id="empty_database" class="checkbox btn btn-mini btn-danger btn-checkbox"><?php echo Form::checkbox( 'install[empty_database]', 1, (bool) Arr::get( $data,'empty_database'));?> <?php echo __('Empty database'); ?></label>
						
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="installDBPrefixField"><?php echo __( 'Prefix' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[db_table_prefix]', Arr::get( $data, 'db_table_prefix' ), array(
							'class' => 'input-small', 'id' => 'installDBPrefixField'
						) ); ?>

						<p class="help-block"><?php echo __( 'Usefull to prevent conflicts if you have, or plan to have, multiple :cms installations with a single database.', array(':cms' => CMS_NAME) ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
 
    <h1><?php echo __( 'Site information' ); ?></h1>
    <div>
		<div class="widget">
			<div class="widget-content">
				<div class="control-group">
					<label class="control-label" for="installSiteNameField"><?php echo __( 'Site title' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[site_name]', Arr::get( $data, 'site_name' ), array(
							'class' => 'span7', 'id' => 'installSiteNameField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="installUsernameField"><?php echo __( 'Administrator username' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[username]', Arr::get( $data, 'username' ), array(
							'class' => 'input-medium', 'id' => 'installUsernameField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>
				<div class="control-group well well-small">
					<label class="control-label" for="userEditPasswordField"><?php echo __('Password'); ?></label>
					<div class="controls">
						<div id="password-form">
							<?php echo Form::password('install[password_field]', Arr::get( $data,'password_field'), array(
								'class' => 'input-medium', 'id' => 'userEditPasswordField'
							)); ?>

							<?php echo Form::password('install[password_confirm]', Arr::get( $data,'password_confirm'), array(
								'class' => 'input-medium', 'id' => 'userEditPasswordConfirmField', 'placeholder' => __('Confirm Password')
							)); ?>

							<p class="help-block"><?php echo __('At least :num characters. Must be unique.', array(
								':num' => Kohana::$config->load('auth')->get( 'password_length', 5 )
							)); ?>
							<hr />
						</div>
						<label class="checkbox btn btn-success btn-checkbox">
							<?php echo Form::checkbox( 'install[password_generate]', 1, (bool) Arr::get( $data,'password_generate'), array(
								'id' => 'generate-password-checkbox'
							)); ?> <?php echo __( 'Generate password' ); ?>
						</label>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="installEmailField"><?php echo __( 'Administrator email' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[email]', Arr::get( $data, 'email' ), array(
							'class' => 'input-medium', 'id' => 'installEmailField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>
				<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'install[locale]', 'options' => I18n::available_langs()
					))
					->selected(Arr::get( $data, 'locale' ))
					->label(__('Interface language'))
				)); ?>
				<hr />
				<div class="control-group">
					<label class="control-label" for="installAdminDirNamexField"><?php echo __( 'Admin dir name' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[admin_dir_name]', Arr::get( $data, 'admin_dir_name' ), array(
							'class' => 'input-small', 'id' => 'installAdminDirNamexField'
						) ); ?> <?php echo UI::label( __( 'Required' ) ); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="installURLSuffixField"><?php echo __( 'URL suffix' ); ?></label>
					<div class="controls">
						<?php echo Form::input( 'install[url_suffix]', Arr::get( $data, 'url_suffix' ), array(
							'class' => 'input-small', 'id' => 'installURLSuffixField'
						) ); ?>

						<p class="help-block"><?php echo __( 'Add a suffix to simulate static html files.' ); ?></p>
					</div>
				</div>
				<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'install[timezone]', 'options' => Date::timezones()
					))
					->selected(Arr::get( $data, 'timezone' ))
					->label(__('Timezone'))
				)); ?>
				
				<hr />
				
				<div class="control-group">
					<label class="control-label"><?php echo __( 'Demo site' ); ?></label>
					<div class="controls">
						<label id="insert-test-data" class="checkbox btn btn-success btn-checkbox">
							<?php echo Form::checkbox( 'install[insert_test_data]', 1, (bool) Arr::get( $data,'insert_test_data'));?> <?php echo __('Install demo site'); ?>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<h1><?php echo __( 'Cache system' ); ?></h1>
	<div>
		<div class="widget">
			<div class="widget-content">

				<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
					'element' => Bootstrap_Form_Element_Select::factory(array(
						'name' => 'install[cache_type]', 'options' => $cache_types
					))
					->selected(Arr::get( $data, 'cache_type' ))
					->label(__('Cache type'))
				)); ?>
			</div>
		</div>
	</div>
</div>

<?php echo Form::close(); ?>