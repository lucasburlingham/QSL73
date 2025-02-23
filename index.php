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
			<input type="text" name="name" placeholder="Search for a name">
			<input type="text" name="callsign" placeholder="Search for a callsign">
			<button type="submit">
				<img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIiBjbGFzcz0ibHVjaWRlIGx1Y2lkZS1zZWFyY2giPjxjaXJjbGUgY3g9IjExIiBjeT0iMTEiIHI9IjgiLz48cGF0aCBkPSJtMjEgMjEtNC4zLTQuMyIvPjwvc3ZnPg=="
					alt="Search icon" style="color:white; height: 1.25em; vertical-align: top;">
				Search
			</button>
		</form>
	</div>
	<div class="results">
		<?php
		// Search for callsign or name or both
		if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['callsign']) && !empty($_POST['callsign']))
		{
			$db = new SQLite3('callsigns.sqlite');
			$name = $_POST['name'];
			$callsign = $_POST['callsign'];
			$query = $db->prepare('SELECT * FROM callsign WHERE fullname LIKE :name AND callsign LIKE :callsign');
			$query->bindValue(':name', "%$name%");
			$query->bindValue(':callsign', "%$callsign%");
			$result = $query->execute();
			echo "<p>Results for <i>\"$name\" and \"$callsign\"</i></p>";

			// create a table to display the results
			echo "<table>";
			echo "<tr><th>Callsign</th><th>Full Name</th><th>Address</th><th>Action</th></tr>";

			while ($row = $result->fetchArray())
			{
				echo "<tr><td>" . $row["callsign"] . "</td><td>" . $row['fullname'] . "</td><td>" . $row['address'] . "</td><td><a href='/log.php?callsign=" . $row["callsign"] . "&log_qso=true'>Log QSO</a> <a href='https://www.pskreporter.info/pskmap.html?preset&callsign=" . $row["callsign"] . "&txrx=tx&timerange=21600'>View Reception Report</a></td></tr>";
			}

			echo "</table>";

			$db->close();
		}
		else
		{
			if (isset($_POST['name']) && !empty($_POST['name']))
			{
				$db = new SQLite3('callsigns.sqlite');
				$name = $_POST['name'];
				// Search for the name in the database
				$query = $db->prepare('SELECT * FROM callsigns WHERE fullname LIKE :name');
				$query->bindValue(':name', "%$name%");
				$result = $query->execute();
				$row = $result->fetchArray();
				echo "<p><i>Results for \"$name\"</i></p>";

				// create a table to display the results
				echo "<table>";
				echo "<tr><th>Callsign</th><th>Full Name</th><th>Address</th><th>Action</th></tr>";

				while ($row = $result->fetchArray())
				{
					echo "<tr><td>" . $row["callsign"] . "</td><td>" . $row['fullname'] . "</td><td>" . $row['address'] . "</td><td><a href='/log.php?callsign=" . $row["callsign"] . "&log_qso=true'>Log QSO</a> <a href='https://www.pskreporter.info/pskmap.html?preset&callsign=" . $row["callsign"] . "&txrx=tx&timerange=21600'>View Reception Report</a></td></tr>";
				}

				echo "</table>";

				echo '<button onclick="window.location.href = window.location.pathname;" style="margin-bottom: 5em;">Clear Results</button>';
				$db->close();
			}

			if (isset($_POST['callsign']) && !empty($_POST['callsign']))
			{
				$db = new SQLite3('callsigns.sqlite');
				$callsign = $_POST['callsign'];
				$query = $db->prepare('SELECT * FROM callsigns WHERE callsign LIKE :callsign');
				$query->bindValue(':callsign', "%$callsign%");
				$result = $query->execute();
				echo "<p>Results for callsign <i>\"$callsign\"</i></p>";


				// create a table to display the results
				echo "<table>";
				echo "<tr><th>Callsign</th><th>Full Name</th><th>Address</th><th>Action</th></tr>";

				while ($row = $result->fetchArray())
				{
					echo "<tr><td>" . $row["callsign"] . "</td><td>" . $row['fullname'] . "</td><td>" . $row['address'] . "</td><td><a href='/log.php?callsign=" . $row["callsign"] . "&log_qso=true'>Log QSO</a> <a href='https://www.pskreporter.info/pskmap.html?preset&callsign=" . $row["callsign"] . "&txrx=tx&timerange=21600'>View Reception Report</a></td></tr>";
				}

				echo "</table>";

				echo '<button onclick="window.location.href = window.location.pathname;" style="margin-bottom: 5em;">Clear Results</button>';

				$db->close();
			}
		}


		?>
	</div>

	<footer class="centered">
		&copy; 2025 Lucas Burlingham. All rights reserved. Find updates at: <a href="https://lucasburlingham.me/KR4AUW"
			target="_blank" rel="noreferrer noopener">lucasburlingham.me/KR4AUW</a>
	</footer>

	<script src="/assets/script.js"></script>
</body>

</html>