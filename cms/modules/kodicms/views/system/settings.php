<?php echo Form::open(Route::get('api')->uri(array('controller' => 'settings', 'action' => 'save')), array(
	'id' => 'settingForm', 'class' => 'form-horizontal form-ajax'
)); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<div class="widget">
		<div class="tabbable tabs-left">
			<ul class="nav nav-tabs"></ul>
			<div class="tab-content"></div>
		</div>
		<div class="widget-header spoiler-toggle" data-spoiler=".site-information-content" data-icon="info">
			<h3><?php echo __( 'Site information' ); ?></h3>
		</div>
		<div class="widget-content spoiler site-information-content">
			<div class="control-group">
				<label class="control-label title" for="settingTitle"><?php echo __( 'Site title' ); ?></label>
				<div class="controls">
					<?php echo Form::input( 'setting[site][title]', Config::get('site', 'title' ), array(
						'class' => 'input-title input-block-level', 'id' => 'settingTitle'
					) ); ?>
					<p class="help-block"><?php echo __( 'This text will be present at backend and can be used in frontend pages.' ); ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="settingDescription"><?php echo __( 'Site description' ); ?></label>
				<div class="controls">
					<?php echo Form::textarea( 'setting[site][description]', Config::get('site', 'description' ), array(
						'id' => 'settingDescription', 'class' => 'input-block-level', 'rows' => 3
					) ); ?>
				</div>
			</div>
		</div>
		<div class="widget-header" data-icon="globe">
			<h3><?php echo __( 'Site settings' ); ?></h3>
		</div>
		<div class="widget-content">
			<div class="control-group">
				<?php echo Form::label('setting_default_locale', __('Default interface language'), array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo Form::select('setting[site][default_locale]', I18n::available_langs(), Config::get('site', 'default_locale'), array('id' => 'setting_default_locale')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo Form::label('setting_date_format', __('Date format'), array('class' => 'control-label')); ?>
				<div class="controls">
					<?php echo Form::select('setting[site][date_format]', $dates, Config::get('site', 'date_format'), array('id' => 'setting_date_format')); ?>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Default backend section' ); ?></label>
				<div class="controls">
					<?php echo Form::select('setting[site][default_tab]', $site_pages, Config::get('site', 'default_tab')); ?>
					<p class="help-block"><?php echo __( 'This allows you to specify which section you will see by default after login.' ); ?></p>
				</div>
			</div>
			<h3><?php echo __( 'Debug' ); ?></h3>
			<hr />
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Profiling' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][profiling]', Form::choises(), Config::get('site', 'profiling' )); ?>
					<p class="help-block"><?php echo __('For detailed profiling use Kohana::$enviroment = Kohana::DEVELOPMENT or SetEnv KOHANA_ENV DEVELOPMENT in .htaccess'); ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Debug mode' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][debug]', Form::choises(), Config::get('site', 'debug' )); ?>
				</div>
			</div>
			<hr />
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Revision templates' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][templates_revision]', Form::choises(), Config::get('site', 'templates_revision' )); ?>
					<p class="help-block"><?php echo __( 'After save layouts or snippets create revision copy in logs directory' ); ?></p>
				</div>
			</div>
			<h3><?php echo __( 'Design' ); ?></h3>
			<hr />
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Show breadcrumbs' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][breadcrumbs]', Form::choises(), Config::get('site', 'breadcrumbs', Config::NO )); ?>
				</div>
			</div>
		</div>
		<div class="widget-header spoiler-toggle" data-spoiler=".page-options-container" data-icon="edit">
			<h3><?php echo __( 'Page settings' ); ?></h3>
		</div>
		<div class="widget-content spoiler page-options-container">
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Default page status' ); ?> </label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][default_status_id]', $default_status_id, Config::get('site', 'default_status_id' )); ?>
					<p class="help-block"><?php echo __( 'This status will be autoselected when page creating.' ); ?></p>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo __( 'Default filter' ); ?></label>
				<div class="controls">
					<?php echo Form::select('setting[site][default_filter_id]', $filters, Config::get('site', 'default_filter_id' )); ?>
					<p class="help-block"><?php echo __( 'Only for filter in pages, <i>not</i> in snippets.' ); ?></p>
				</div>
			</div>
			<hr />
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Find similar pages' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[site][find_similar]', Form::choises(), Config::get('site', 'find_similar' )); ?>
					<p class="help-block"><?php echo __( 'If requested page url is incorrect, then find similar page.' ); ?></p>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo __( 'Check page date' ); ?></label>
				<div class="controls">
					<?php echo Form::select( 'setting[page][check_date]', Form::choises(), Config::get('site', 'check_page_date', Config::NO ));?>
				</div>
			</div>
		</div>
		<?php Observer::notify( 'view_setting_plugins' ); ?>
		<div class="form-actions widget-footer">
			<?php echo Form::button( 'submit', UI::icon( 'ok' ) . ' ' . __( 'Save settings' ), array(
				'class' => 'btn btn-large'
			) ); ?>
		</div>
	</div>
<?php Form::close(); ?>