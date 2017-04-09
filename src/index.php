<?php include('app/init.php'); ?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Hello World!</title>
	</head>
	<body>
		<h1>This is a test for the DB Class: listing all databases</h1>
		<?php
			echo "<pre>";
			$db = new _2ndhand\DB();
			$res = $db->fetch('pg_catalog.pg_database');
			//$res = $db->tableExists('pg_catalog.pg_database');
			print_r($res);
			echo "</pre>";

			// print php general info
			phpinfo();
			
		?>		
	</body>
</html>