<div class="widget">
	<div class="tabbable tabs-left">
		<ul class="nav nav-tabs"></ul>
		<div class="tab-content"></div>
	</div>
	
	<div class="widget-header"><h3><?php echo __( 'General information' ); ?></h3></div>
	<div class="widget-content">
		<table class="table table-striped">
			<colgroup>
				<col width="200px" />
				<col />
			</colgroup>
			<tbody>
				<tr>
					<th><?php echo __('CMS name') ?></th>
					<td><?php echo CMS_NAME; ?></td>
				</tr>
				<tr>
					<th><?php echo __('CMS Version') ?></th>
					<td><?php echo CMS_VERSION; ?></td>
				</tr>
				<tr>
					<th><?php echo __('PHP Version') ?></th>
					<td><?php echo PHP_VERSION; ?></td>
				</tr>
				<tr>
					<th><?php echo __('Kohana enviroment') ?></th>
					<td><?php echo Arr::get($_SERVER, 'KOHANA_ENV'); ?></td>
				</tr>
				<tr>
					<th><?php echo __('Server host') ?></th>
					<td><?php echo Arr::get($_SERVER, 'HTTP_HOST'); ?></td>
				</tr>
				<tr>
					<th><?php echo __('Web server') ?></th>
					<td><?php echo Arr::get($_SERVER, 'SERVER_SOFTWARE'); ?></td>
				</tr>
				<tr>
					<th><?php echo __('Cache driver') ?></th>
					<td><?php echo Cache::$default; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<?php if(Acl::check('system.phpinfo')): ?>
	<div class="widget-header"><h3><?php echo __( 'PHP info' ); ?></h3></div>
	<div class="widget-content">
	<iframe <?php echo HTML::attributes(array(
	   'src'=>URL::base().Route::get('backend')->uri(array(
	       'controller' => 'system',
			   'action' => 'phpinfo'
          )),
	   'width'=>'100%',
	   'height'=>'500px',
	   'id'=>'phpinfo',
	   'style'=>'border: 0;'
	 )); ?>>
	</iframe>
	</div>
	<?php endif; ?>

	<?php Observer::notify( 'view_system_information' ); ?>
</div>