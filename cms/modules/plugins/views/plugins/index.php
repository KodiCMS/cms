<script id="plugin-item" type="text/template">
	<td class="plugin-name">
		<h5>
			<% if (installed  && settings) { %>
			<%= title %>
			<?php if( ACL::check('plugins.settings')): ?>
			<a href="/<?php echo ADMIN_DIR_NAME; ?>/plugins/settings/<%= id %>" class="btn pull-right">
				<i class="icon-cog"></i> <%= __('Settings') %>
			</a>
			<?php endif; ?>
			<% } else { %>
				<%= title %>
			<% } %>
		</h5>

		<p class="muted"><%= description %></p>
	</td>
	<td class="plugin-version"><%= version %></td>
	<?php if( ACL::check('plugins.change_status')): ?>
	<td class="plugin-status">
		<?php echo UI::button(NULL, array(
			'class' => 'change-status btn btn-mini',
		)); ?>
	</td>
	<?php endif; ?>
</script>

<div id="pluginsMap" class="widget widget-nopad">
	<div class="widget-header"></div>
	<div class="widget-content">
		<table class="table table-striped table-hover" id="PluginsList">
			<colgroup>
				<col />
				<col width="80px" />
				<?php if( ACL::check('plugins.change_status')): ?>
				<col width="100px" />
				<?php endif; ?>
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('Plugin name'); ?></th>
					<th><?php echo __('Version'); ?></th>
					
					<?php if( ACL::check('plugins.change_status')): ?>
					<th><?php echo __('Actions'); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>