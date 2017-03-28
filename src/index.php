<?php include('../postgresql-www-data.php'); ?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Hello World!</title>
	</head>
	<body>
		<?php

			// test db connection by listing all tables
			$db = new PDO("pgsql:host={$DBCONFIG['HOST']};dbname={$DBCONFIG['NAME']}", $DBCONFIG['USER'], $DBCONFIG['PASS']);
			$stmt = $db->query('SELECT * FROM pg_catalog.pg_tables');
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// print results
			echo "<pre>";
			var_dump($res);
			echo "</pre>";

			// print php general info
			phpinfo();
			
		?>		
	</body>
</html>