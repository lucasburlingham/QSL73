<?php

require 'db.php';

?>
<html>

<head>
	<title>QSL;73 Log</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/assets/style.css" rel="stylesheet">
</head>

<body>

	<?php
	include 'nav.php';
	?>
	<div class="form">
		<form action="index.php" method="post">
			<input type="text" name="callsign" placeholder="Callsign">
			<input type="text" name="grid" placeholder="Grid">
			<input type="time" name="time" placeholder="Time" value="">

			<script>
				var d = new Date();
				var h = d.getHours();
				var m = d.getMinutes();
				var time = h + ":" + m;
				document.getElementsByName('time')[0].value = time;
			</script>
			<button type="submit">Search</button>
		</form>
	</div>
	<div class="results">

		<?php

		// Insert log (callsign, grid)
		if (isset($_POST['callsign']) && !empty($_POST['callsign']) && isset($_POST['grid']) && !empty($_POST['grid']))
		{
			$db = new SQLite3('callsigns.sqlite');

			$callsign = $_POST['callsign'];
			$grid = $_POST['grid'];

			// Get UTC Date and time
			date_default_timezone_set('UTC');
			$curr_date = date('Y-m-d H:i:s');

			$query = $db->prepare('INSERT INTO logs (callsign, grid, curr_date) VALUES (:callsign, :grid, :curr_date)');
			$query->bindValue(':callsign', $callsign);
			$query->bindValue(':grid', $grid);
			$query->execute();
			$db->close();
		}
		else if ((isset($_GET['callsign']) && !empty($_GET['callsign'])) && ($_GET['log_qso'] === 'true'))
		{
			$db = new SQLite3('callsigns.sqlite');

			$callsign = $_GET['callsign'];
			$grid = $_GET['grid'];

			// Get UTC Date and time
			date_default_timezone_set('UTC');
			$curr_date = date('Y-m-d H:i:s');

			$query = $db->prepare('INSERT INTO logs (callsign, grid, curr_date) VALUES (:callsign, :grid, :curr_date)');
			$query->bindValue(':callsign', $callsign);
			$query->bindValue(':grid', $grid);
			$query->execute();
			$db->close();
		}

		// Display logs
		$db = new SQLite3('callsigns.sqlite');
		$result = $db->query('SELECT * FROM logs');

		// add table
		echo "<table>";
		echo "<tr><th>Callsign</th><th>Grid</th><th>Time</th></tr>";
		
		while ($row = $result->fetchArray())
		{
			echo "<tr><td>" . $row["callsign"] . "</td><td>" . $row['grid'] . "</td><td>" . $row['curr_date'] . "</td></tr>";
		}

		echo "</table>";
		?>

		<!-- export adif file button -->
		<form action="export.php" method="post">
			<button type="submit">Export ADIF File</button>
		</form>
	</div>


	<footer class="centered">
		&copy; 2025 Lucas Burlingham. All rights reserved. Find updates at: <a href="https://lucasburlingham.me/KR4AUW"
			target="_blank" rel="noreferrer noopener">lucasburlingham.me/KR4AUW</a>
	</footer>


	<script>
		document.addEventListener('DOMContentLoaded', () => {
			// reload the page without the search query in the URL so that the user can refresh the page without relogging the QSO
			window.history.replaceState({}, document.title, "/");
		});
	</script>
	<script src="/assets/script.js"></script>
</body>

</html>