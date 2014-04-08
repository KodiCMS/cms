<div class="widget">
<?php echo Form::open(Request::current()->uri(), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>
	<?php echo Form::hidden('id', $widget->id); ?>
	<div class="widget-header spoiler-toggle" data-spoiler=".general-spoiler" data-hash="description">
		<h4><?php echo __('Widget Information'); ?></h4>
	</div>
	<div class="widget-content spoiler general-spoiler">
		<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'name', 'value' => $widget->name
			))
			->label(__('Widget Header'))
			->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
		));

		echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Textarea::factory(array(
				'name' => 'description', 'body' => $widget->description
			))
			->label(__('Widget Description'))
		)); ?>
	</div>

	<?php if($widget->use_template): ?>
	<?php echo View::factory('helper/snippet_select', array(
		'header' => __('Widget template'),
		'template' => $widget->template,
		'default' => $widget->default_template() ? UI::button(__('Default template'), array(
			'href' => Route::get('backend')->uri(array(
				'controller' => 'widgets', 
				'action' => 'template',
				'id' => $widget->id
			)), 'icon' => UI::icon('desktop'),
			'class' => 'popup fancybox.iframe btn'
		)) : NULL
	)); ?>
	<?php endif; ?>

	<?php if($widget->use_caching AND ACL::check('widgets.cache')): ?>
	<div class="widget-content">
		<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Checkbox::factory(array(
				'name' => 'caching', 'value' => 1
			), array('id' => 'caching'))
			->checked($widget->caching)
			->label(__('Cache enabled'))
		)); ?>
		
		<div id="cache_settings_container">
			<div id="cache_lifetime_group" class="control-group">
				<label class="control-label" for="cache_lifetime"><?php echo __('Cache lifetime'); ?></label>
				<div class="controls">
					<input type="text" id="cache_lifetime" name="cache_lifetime" value="<?php echo $widget->cache_lifetime; ?>" class="input-medium">

					<span class="flags" id="cache_lifetime_labels">
						<span class="label" data-value="<?php echo Date::MINUTE; ?>"><?php echo __('Minute'); ?></span> 
						<span class="label" data-value="<?php echo Date::HOUR; ?>"><?php echo __('Hour'); ?></span>
						<span class="label" data-value="<?php echo Date::DAY; ?>"><?php echo __('Day'); ?></span>
						<span class="label" data-value="<?php echo Date::WEEK; ?>"><?php echo __('Week'); ?></span>
						<span class="label" data-value="<?php echo Date::MONTH; ?>"><?php echo __('Month'); ?></span>
						<span class="label" data-value="<?php echo Date::YEAR; ?>"><?php echo __('Year'); ?></span>
					</span>
				</div>
			</div>

			<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
				'element' => Bootstrap_Form_Element_Textarea::factory(array(
					'name' => 'cache_tags', 'body' => $widget->cache_tags()
				))
				->attributes('class', 'tags')
				->label(__('Cache tags'))
			)); ?>
			
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ACL::check( 'widgets.roles' ) ): ?>
	<div class="widget-header spoiler-toggle" data-spoiler=".roles-spoiler" data-hash="roles">
		<h4><?php echo __('Widget permissions'); ?></h4>
	</div>
	<div class="widget-content spoiler roles-spoiler">
		<div class="controls">
			<?php echo Form::select('roles[]', $roles, $widget->roles, array(
				'class' => 'span12'
			)); ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="widget-header">
		<h4><?php echo __('Widget parameters'); ?></h4>
	</div>
	<?php if($widget->use_template): ?>
	<div class="widget-content">
		<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
			'element' => Bootstrap_Form_Element_Input::factory(array(
				'name' => 'header', 'value' => $widget->header
			))
			->label(__('Header'))
			->attributes('class', Bootstrap_Form_Element_Input::BLOCK_LEVEL)
		)); ?>
	</div>
	<?php endif; ?>
	<?php echo $content; ?>

	<?php if( ACL::check('widgets.location') ): ?>
	<div class="widget-content ">
		<?php echo Bootstrap_Element_Button::factory(array(
			'href' => Route::get('backend')->uri(array(
					'controller' => 'widgets', 
					'action' => 'location',
					'id' => $widget->id)), 
			'title' => __('Widget location')
		), array('target' => 'blank'))->icon('sitemap'); ?>
	</div>
	<?php endif; ?>
	<div class="widget-footer form-actions">
		<?php echo UI::actions($page_name); ?>
	</div>
<?php echo Form::close(); ?>
</div>