<div class="widget-header spoiler-toggle" data-spoiler=".jobs-spoiler" data-icon="bolt">
	<h3><?php echo __('Job settings'); ?></h3>
</div>
<div class="widget-content spoiler jobs-spoiler">
	<div class="alert alert-warning">
		<i class="icon icon-lightbulb"></i> <?php echo __('When using cron you need to add the following line to the crontab file:'); ?>
		<br /><br />
		<strong>* * * * * cd <?php echo DOCROOT;?>; php -f index.php --task=job:run &gt; /dev/null 2&gt;&amp;1</strong>
	</div>
				
	<div class="control-group">
		<label class="control-label"><?php echo __( 'Job agent' ); ?></label>
		<div class="controls">
			<?php echo Form::select( 'setting[job][agent]', Model_Job::agents(), (int) Config::get('job', 'agent')); ?>
		</div>
	</div>
</div>