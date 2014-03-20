<div class="widget-header">
	<h3><?php echo __('Page options'); ?></h3>
</div>

<div class="widget-content">
	<?php if( $page->id != 1 ): ?>
	<?php echo $page->label('parent_id'); ?>
	<?php echo $page->field('parent_id', array(
		'prefix' => 'page',
		'class' => 'span12'
	)); ?>

	<br />
	<?php endif; ?>

	<?php echo $page->label('layout_file', array('class' => 'pull-left')); ?>
	
	<?php if( empty($page->layout_file) ): ?>
	<span class="pull-right">
		<?php echo UI::label(__('Current layput: :name', array(':name' => $page->layout()))); ?>
	</span>
	<?php endif; ?>
	<div class="clearfix"></div>

	<?php echo $page->field('layout_file', array(
		'prefix' => 'page',
		'class' => 'span12'
	)); ?>
	<br />

	<div class="well well-small">
		<?php echo $page->label('behavior_id'); ?>
		<?php echo $page->field('behavior_id', array(
			'prefix' => 'page',
			'class' => 'span12'
		)); ?>
		<div id="behavor_options"></div>
	</div>

	<?php if( $page->id != 1 ): ?>
	<div class="page-statuses">
		<?php echo $page->label('status_id'); ?>
		<?php echo $page->field('status_id', array(
			'prefix' => 'page',
			'class' => 'span12'
		)); ?>

		<div class="hidden password-container">
			<br />
			<?php echo $page->label('password'); ?>
			<?php echo $page->field('password', array(
				'prefix' => 'page',
				'class' => 'span12'
			)); ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if( $page->id != 1 ): ?>
	
	<?php echo $page->label('published_on'); ?>
	<?php echo $page->field('published_on', array(
		'prefix' => 'page',
		'class' => 'span12 datetimepicker'
	)); ?>
	<?php endif; ?>

	<?php if ( ACL::check( 'page.permissions' ) ): ?>
	<?php echo $page->label('needs_login'); ?>
	<?php echo $page->field('needs_login', array(
		'prefix' => 'page',
		'class' => 'span12'
	)); ?>
	<?php endif; ?>

	<?php if ( ACL::check( 'page.permissions' ) ): ?>
	<br />
	<div class="well well-small">
		<?php echo $page->label('page_permissions'); ?>
		<?php echo Form::select('page_permissions[]', $permissions, array_keys($page_permissions), array(
			'class' => 'span12'
		)); ?>
	</div>
	<?php endif; ?>
</div>

<?php Observer::notify('view_page_edit_options', array($page)); ?>