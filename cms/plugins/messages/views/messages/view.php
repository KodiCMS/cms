<script>
	var MESSAGE_ID = <?php echo $message->id; ?>;
</script>
<style>
	.widget-content.new-message {
		background: #e8efcf
	}
	.widget-content.own-message {
		background: #FCF8E3
	}
	
	.message-text {
		border-top: 1px solid #ddd;
		padding-top: 5px;
	}
</style>

<div class="widget">
	<div class="widget-header">
		<h3><small><?php echo __('Subject'); ?>:</small> <?php echo $message->title; ?></h3>
	</div>
	
	<?php echo $tpl->set('message', $message); ?>
	
	<div class="widget-content ">
		<?php echo Form::open(); ?>
		<h4><?php echo __('Answer'); ?></h4>
		<?php echo Form::hidden('parent_id', $message->id); ?>
		<?php echo Form::textarea('content', NULL, array('id' => 'message-conent')); ?>
		<script>
		$(function() {
			cms.filters.switchOn( 'message-conent', '<?php echo Config::get('site', 'default_filter_id'); ?>');
		});
		</script>
		<br />
		<?php echo UI::button(__('Send message')); ?>
		<?php echo Form::close(); ?>
	</div>
	
	<div id="messages"></div>
</div>