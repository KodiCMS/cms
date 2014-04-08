<div class="map widget">
	
	<div class="widget-header">
		<div class="row-fluid">
			<?php echo UI::button(__('Send message'), array(
				'href' => Route::get('backend')->uri(array('controller' => 'messages', 'action' => 'add')), 'icon' => UI::icon('envelope'),
				'class' => 'btn'
			)); ?>
		</div>
	</div>
	
	<?php if(count($messages) > 0): ?>
	<div class="widget-content widget-nopad">
		<table class=" table table-striped table-hover" id="MessagesList">
			<colgroup>
				<col />
				<col width="150px" />
				<col width="180px" />
				<col width="100px" />
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Message title'); ?></th>
					<th><?php echo __('Message author'); ?></th>
					<th><?php echo __('Date created'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($messages as $message): ?>
				<tr data-id="<?php echo $message->id; ?>" <?php if( $message->is_read == Model_API_Message::STATUS_NEW): ?>class="info"<?php endif; ?>>
					<td><?php echo HTML::anchor(Route::get('backend')->uri(array('controller' => 'messages', 'action' => 'view', 'id' => (int) $message->id)), $message->title); ?></td>
					<td><?php echo $message->author; ?></td>
					<td><?php echo Date::format($message->created_on); ?></td>
					<td class="actions">
						<?php echo UI::button(NULL, array(
							'icon' => UI::icon( 'remove' ),
							'class' => 'btn btn-mini btn-confirm btn-remove'
						)); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php else: ?>
	<div class="widget-content">
		<h1><?php echo __('You dont have messages'); ?></h1>
	</div>
	<?php endif; ?>
	
</div>