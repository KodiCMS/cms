<footer>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span6">
				<p><?php echo __('Thank you for using :site', array(':site' => HTML::anchor(CMS_SITE, CMS_NAME))); ?></p>
			</div>
			<div class="span6 text-right">
				<p>
				&copy; 2012<?php echo (date('Y') > 2012) ? ' - ' . date('Y') : ''; ?> <?php echo HTML::anchor( CMS_SITE, CMS_NAME ) ?> v<?php echo CMS_VERSION; ?> | 
				<?php echo __('Powered by :framework v:version :codename', array(
					':framework' => HTML::anchor( 'http://kohanaframework.org/', 'Kohana' ), 
					':version' => Kohana::VERSION, 
					':codename' => Kohana::CODENAME)); ?></p>
			</div>
		</div>
	</div>
</footer>