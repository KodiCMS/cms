<?php

if (version_compare(PHP_VERSION, '5.3', '<'))
{
	// Clear out the cache to prevent errors. This typically happens on Windows/FastCGI.
	clearstatcache();
}
else
{
	// Clearing the realpath() cache is only possible PHP 5.3+
	clearstatcache(TRUE);
}

$failed = FALSE;
?>

<div id="env_test widget-content">
	<table class="table table-striped">
		<tr>
			<th><?php echo __('PHP Version'); ?></th>
			<?php if (version_compare(PHP_VERSION, '5.3.3', '>=')): ?>
				<td class="pass"><?php echo PHP_VERSION ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><?php echo __('Kohana requires PHP 5.3.3 or newer, this version is :version.', array(':version' => PHP_VERSION)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th><?php echo __('System Directory'); ?></th>
			<?php if (is_dir(SYSPATH) AND is_file(SYSPATH.'classes/kohana'.EXT)): ?>
				<td class="pass"><?php echo SYSPATH ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><?php echo __('The configured :dir directory does not exist or does not contain required files.', array(
					':dir' => '<code>system</code>'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th><?php echo __('Application Directory'); ?></th>
			<?php if (is_dir(APPPATH) AND is_file(APPPATH.'bootstrap'.EXT)): ?>
				<td class="pass"><?php echo APPPATH ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><?php echo __('The configured :dir directory does not exist or does not contain required files.', array(
					':dir' => '<code>application</code>'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th><?php echo __('Cache Directory'); ?></th>
			<?php if (is_dir(CMSPATH) AND is_dir(CMSPATH.'cache') AND is_writable(CMSPATH.'cache')): ?>
				<td class="pass"><?php echo CMSPATH.'cache/' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><?php echo __('The :dir directory is not writable.', array(
					':dir' => '<code>'.CMSPATH.'cache/</code>'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th><?php echo __('Logs Directory'); ?></th>
			<?php if (is_dir(CMSPATH) AND is_dir(CMSPATH.'logs') AND is_writable(CMSPATH.'logs')): ?>
				<td class="pass"><?php echo CMSPATH.'logs/' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">The <code><?php echo CMSPATH.'logs/' ?></code> directory is not writable.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th><?php echo __('Config file placement'); ?></th>
			<?php if (is_dir(pathinfo(CFGFATH, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR) AND ! is_file( CFGFATH) AND is_writable( pathinfo(CFGFATH, PATHINFO_DIRNAME) )): ?>
				<td class="pass">
					<?php echo CFGFATH; ?>
					<div class="text-warning"><?php echo __('To change config file placement edit index.php file'); ?></div>
				</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">
					<?php if(!is_writable( pathinfo(CFGFATH, PATHINFO_DIRNAME) )): ?>
					<?php echo __('The config :dir directory must be writable.', array(
						':dir' => pathinfo(CFGFATH, PATHINFO_DIRNAME)
					)); ?>
					<?php else: ?>
					<?php echo __('The config :dir directory does not exist or config file is exists.', array(
						':dir' => CFGFATH, ':file' => pathinfo( CFGFATH, PATHINFO_FILENAME) .'.'. pathinfo( CFGFATH, PATHINFO_EXTENSION)
					)); ?>
					<?php endif; ?>
					<div class="text-warning"><?php echo __('To change config file placement edit index.php file'); ?></div>
				</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>PCRE UTF-8</th>
			<?php if ( ! @preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
				<td class="fail"><a href="http://php.net/pcre" target="blank">PCRE</a> has not been compiled with UTF-8 support.</td>
			<?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
				<td class="fail"><a href="http://php.net/pcre" target="blank">PCRE</a> has not been compiled with Unicode property support.</td>
			<?php else: ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>SPL Enabled</th>
			<?php if (function_exists('spl_autoload_register')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">PHP <a href="http://www.php.net/spl" target="blank">SPL</a> is either not loaded or not compiled in.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Reflection Enabled</th>
			<?php if (class_exists('ReflectionClass')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">PHP <a href="http://www.php.net/reflection" target="blank">reflection</a> is either not loaded or not compiled in.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Filters Enabled</th>
			<?php if (function_exists('filter_list')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">The <a href="http://www.php.net/filter" target="blank">filter</a> extension is either not loaded or not compiled in.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Iconv Extension Loaded</th>
			<?php if (extension_loaded('iconv')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">The <a href="http://php.net/iconv" target="blank">iconv</a> extension is not loaded.</td>
			<?php endif ?>
		</tr>
		<?php if (extension_loaded('mbstring')): ?>
		<tr>
			<th>Mbstring Not Overloaded</th>
			<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE ?>
				<td class="fail">The <a href="http://php.net/mbstring" target="blank">mbstring</a> extension is overloading PHP's native string functions.</td>
			<?php else: ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php endif ?>
		</tr>
		<?php endif ?>
		<tr>
			<th>Character Type (CTYPE) Extension</th>
			<?php if ( ! function_exists('ctype_digit')): $failed = TRUE ?>
				<td class="fail">The <a href="http://php.net/ctype" target="blank">ctype</a> extension is not enabled.</td>
			<?php else: ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>URI Determination</th>
			<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Neither <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, or <code>$_SERVER['PATH_INFO']</code> is available.</td>
			<?php endif ?>
		</tr>
	</table>

	<?php if ($failed === TRUE): ?>
		<p id="results" class="alert alert-error lead"><?php echo UI::icon('remove'); ?> <?php echo __('Kohana may not work correctly with your environment.'); ?></p>
	<?php else: ?>
		<p id="results" class="alert alert-success lead"><?php echo UI::icon('ok'); ?> <?php echo __('Your environment passed all requirements.'); ?></p>
	<?php endif ?>
</div>
<div class="widget-header "><h3><?php echo __( 'Optional Tests' ); ?></h3></div>
<div class="env_test widget-content">
	<p id="info" class="lead alert alert-info"><?php echo UI::icon('lightbulb'); ?> <?php echo __('The following extensions are not required to run the Kohana core, but if enabled can provide access to additional classes.'); ?></p>

	<table class="table table-striped">
		<tr>
			<th>PECL HTTP Enabled</th>
			<?php if (extension_loaded('http')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana can use the :extension extension for the :class class.', array(
					':extension' => '<a href="http://php.net/http" target="blank">http</a>',
					':class' => 'Request_Client_External'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>cURL Enabled</th>
			<?php if (extension_loaded('curl')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana can use the :extension extension for the :class class.', array(
					':extension' => '<a href="http://php.net/curl" target="blank">cURL</a>',
					':class' => 'Request_Client_External'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>mcrypt Enabled</th>
			<?php if (extension_loaded('mcrypt')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana requires :extension for the :class class.', array(
					':extension' => '<a href="http://php.net/mcrypt" target="blank">mcrypt</a>',
					':class' => 'Encrypt'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>GD Enabled</th>
			<?php if (function_exists('gd_info')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana requires :extension for the :class class.', array(
					':extension' => '<a href="http://php.net/gd" target="blank">GD</a>',
					':class' => 'Image'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>MySQL Enabled</th>
			<?php if (function_exists('mysql_connect')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana can use the :extension extension for the :class class.', array(
					':extension' => '<a href="http://php.net/mysql" target="blank">MySQL</a>',
					':class' => 'MySQL'
				)); ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>PDO Enabled</th>
			<?php if (class_exists('PDO')): ?>
				<td class="pass"><?php echo __('Pass'); ?></td>
			<?php else: ?>
				<td class="fail"><?php echo __('Kohana can use the :extension to support additional databases.', array(
					':extension' => '<a href="http://php.net/pdo" target="blank">PDO</a>',
					':class' => 'MySQL'
				)); ?></td>
			<?php endif ?>
		</tr>
	</table>
</div>

<script>var failed = <?php echo $failed ? 'true' : 'false'; ?>;</script>