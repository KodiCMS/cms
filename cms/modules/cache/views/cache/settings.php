<div class="widget-header spoiler-toggle" data-spoiler=".cache-settings" data-hash="cache-settings" data-icon="hdd">
	<h3 id="cache-settings"><?php echo __('Cache settings'); ?></h3>
</div>
<div class="widget-content spoiler cache-settings">
	<?php if( ACL::check('system.cache.clear')): ?>
	<div class="well">
		<?php echo UI::button(__('Clear cache'), array(
			'icon' => UI::icon( 'stethoscope' ),
			'id' => 'clear-cache',
			'class' => 'btn btn-warning'
		)); ?>
	</div>
	<?php endif; ?>
	
	<div class="control-group">
		<?php echo Form::label('setting_cache_driver', __('Cache driver'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::select('', array(
				'file' => __('File cache'), 'apc' => __('APC cache'), 'sqlite' => __('SQLite cache'), 'memcachetag' => __('Memcache')
			), Cache::$default, array('id' => 'setting_cache_driver', 'disabled', 'readonly'));?>
			
			<div class="help-block">
				<i class="icon icon-lightbulb"></i> <?php echo __('The cache driver can change in the config file (:path)', array(':path' => CFGFATH)); ?>
			</div>
		</div>
	</div>
	
	<div class="control-group">
		<?php echo Form::label('setting_cache_status', __('Cache status'), array('class' => 'control-label')); ?>
		<div class="controls">
			<?php echo Form::select('', array('1' => __('Enabled'), '0' => __('Disabled')), (int) (Kohana::$environment === Kohana::PRODUCTION), array('disabled', 'readonly'));?>
		</div>
	</div>
	<hr />
	<div class="control-group">
		<label class="control-label"><?php echo __('Pages cache time'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'setting[cache][front_page]', (int) Config::get('cache', 'front_page'), array(
				'class' => 'input-mini'
			)); ?> <span class="muted"><?php echo __('(Sec.)'); ?></span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo __('Page parts cache time'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'setting[cache][page_parts]', (int) Config::get('cache', 'page_parts'), array(
				'class' => 'input-mini'
			)); ?> <span class="muted"><?php echo __('(Sec.)'); ?></span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo __('Page tags cache time'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'setting[cache][tags]', (int) Config::get('cache', 'tags'), array(
				'class' => 'input-mini'
			)); ?> <span class="muted"><?php echo __('(Sec.)'); ?></span>
		</div>
	</div>
</div>