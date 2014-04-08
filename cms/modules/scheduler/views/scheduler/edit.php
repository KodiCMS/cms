<script>
$(function() {
	$('.interval-label').on('click', function() {
		$('#job-interval').val($(this).data('time'));
	});
})
</script>

<?php echo Form::open(Route::get('backend')->uri(array('controller' => 'scheduler', 'action' => $action, 'id' => $job->id)), array(
	'class' => Bootstrap_Form::HORIZONTAL
)); ?>

<?php echo Form::hidden('token', Security::token()); ?>
<div class="widget">
	
	<div class="widget-header">
		<h4><?php echo __('General information'); ?></h4>
		
		<?php if($job->loaded() AND  Acl::check( 'scheduler.run')): ?>
		<?php echo UI::button(__('Run job'), array(
			'href' => Route::get('backend')->uri(array('controller' => 'scheduler', 'action' => 'run', 'id' => $job->id)), 'icon' => UI::icon('play')
		)); ?>
		<?php endif; ?>
	</div>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label title" for="name"><?php echo __( 'Job name' ); ?></label>
			<div class="controls">
				<?php
				echo Form::input( 'name', $job->name, array(
					'class' => 'input-title input-block-level', 'id' => 'subject'
				) );
				?>
			</div>
		</div>
	
		<div class="control-group">
			<label class="control-label" for="job"><?php echo __( 'Job function' ); ?></label>
			<div class="controls">
				<?php echo Form::select( 'job', $types, $job->job, array(
					'id' => 'job'
				) ); ?>
			</div>
		</div>
	</div>
	
	<div class="widget-header">
		<h4><?php echo __('Runing options'); ?></h4>
	</div>
	<div class="widget-content">
		<div class='well'>
			<?php echo __('Job run start'); ?> <?php echo Form::input('date_start', $job->date_start(), array('class' => 'datetimepicker input-medium')); ?> -
			<?php echo __('Job run end'); ?> <?php echo Form::input('date_end', $job->date_end(), array('class' => 'datetimepicker input-medium')); ?>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo __('Interval'); ?></label>
			<div class="controls">
				<?php echo Form::input('interval', $job->interval, array('class' => 'input-medium', 'id' => 'job-interval')); ?>
				<span class="label interval-label" data-time="<?php echo Date::MINUTE; ?>"><?php echo __('Minute'); ?></span> 
				<span class="label interval-label" data-time="<?php echo Date::HOUR; ?>"><?php echo __('Hour'); ?></span>
				<span class="label interval-label" data-time="<?php echo Date::DAY; ?>"><?php echo __('Day'); ?></span>
				<span class="label interval-label" data-time="<?php echo Date::WEEK; ?>"><?php echo __('Week'); ?></span>
				<span class="label interval-label" data-time="<?php echo Date::MONTH; ?>"><?php echo __('Month'); ?></span>
				<span class="label interval-label" data-time="<?php echo Date::YEAR; ?>"><?php echo __('Year'); ?></span>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<?php echo __('Or'); ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo __('Crontime string'); ?></label>
			<div class="controls">
				<?php echo Form::input('crontime', $job->crontime, array('class' => 'input-large', 'id' => 'job-crontime')); ?>
				<p class="help-inline"><?php echo HTML::anchor('http://ru.wikipedia.org/wiki/Cron', __('Crontab help'), array(
					'target' => 'blank'
				)); ?></p>
			</div>
		</div>
	</div>
	<div class="form-actions widget-footer">
		<?php echo UI::actions($page_name); ?>
	</div>
</div>
<?php Form::close(); ?>

<?php if(!empty($history)): ?>
<?php echo $history; ?>
<?php endif; ?>
