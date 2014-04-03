<html>
	<head>
		<title><?php echo $subject; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
		<h1><?php echo $subject; ?></h1>
		
		<hr />
		<?php foreach($data as $title => $value): ?>
		<p><strong><?php echo $title; ?>:</strong> <?php echo $value; ?></p>
		<?php endforeach; ?>
	</body>
</html>