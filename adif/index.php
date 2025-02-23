<?php
// set the session cookie id
session_id('qsl73');

// Persist the session forever since it's a single user application
session_start();


// Add session variables
if (!isset($_POST['STATION_CALLSIGN']))
{
	$_SESSION['STATION_CALLSIGN'] = $_POST['STATION_CALLSIGN'];
}


if (isset($_POST['submit']))
{
	if ($_POST['initial'] == "false")
	{
		// Just add to the existing adif file
		// Get the adif file from the session since we're logging multiple QSOs
		$adif = $_SESSION['adif'];

		// Add the new QSO
		$adif = addQSO($adif);

		// EOR is added outside of the loop to prevent adding it multiple times
	}
	else if ($_POST['initial'] == "true")
	{
		// Create a new adif file
		$adif = '';

		// Set the program ID and version
		$program_id = 'QSL;73';
		$program_id_version = '1.0';


		// Add the header
		$adif = adifHeader($adif, $program_id, $program_id_version);

		// Add each QSO as they come in
		$adif = addQSO($adif);

	}
	else
	{
		die('Invalid INITIAL flag value. Please try again with either TRUE or FALSE.');
	}

	// Save the adif to the session
	$_SESSION['adif'] = $adif;

	// Save the adif to a cookie named 'qsl73'
	setcookie('qsl73', $adif, time() + 60 * 60 * 24 * 365, '/');
	// Save it to localStorage
	echo "<script>localStorage.setItem('qsl73', '" . $adif . "');</script>";

}





function adifHeader($adif, $program_id, $program_id_version)
{
	$adif .= "<ADIF_VER:5>3.0.5\n";
	$adif .= "<PROGRAMID:" . strlen($program_id) . ">" . $program_id . "\n";
	$adif .= "<PROGRAMVERSION:" . strlen($program_id_version) . ">" . $program_id_version . "\n";
	$adif .= "<MY_SIG:4>POTA\n";
	$adif .= "<MY_SIG_INFO:" . strlen($_POST['PARK']) . ">" . $_POST['PARK'] . "\n";
	$adif .= "<EOH>\n";

	return $adif;
}

function addQSO($adif)
{
	$qso_date = date('Ymd', strtotime($_POST['QSO_DATE']));
	$time_on = date('Hi', strtotime($_POST['TIME_ON']));

	// convert to UTC
	$utc = new DateTime($qso_date . ' ' . $time_on, new DateTimeZone('UTC'));
	$utc->setTimezone(new DateTimeZone('UTC'));
	$qso_date = $utc->format('Ymd');
	$time_on = $utc->format('Hi');

	$adif .= "<STATION_CALLSIGN:" . strlen($_POST['STATION_CALLSIGN']) . ">" . $_POST['STATION_CALLSIGN'] . "\n";
	$adif .= "<CALL:" . strlen($_POST['CALL']) . ">" . $_POST['CALL'] . "\n";
	$adif .= "<QSO_DATE:" . strlen($qso_date) . ">" . $qso_date . "\n";
	$adif .= "<TIME_ON:" . strlen($time_on) . ">" . $time_on . "\n";
	$adif .= "<BAND:" . strlen($_POST['BAND']) . ">" . $_POST['BAND'] . "\n";
	$adif .= "<MODE:" . strlen($_POST['MODE']) . ">" . $_POST['MODE'] . "\n";
	$adif .= "<EOR>\n";

	return $adif;
}

session_write_close();
?>
<!DOCTYPE html>
<html>

<head>
	<title>QSL;73 Log</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/assets/style.css" rel="stylesheet">
</head>

<body>
	<h4 class="titlebar">QSL;73 Log <span id="current_callsign"></spa>
	</h4>
	<h1>UTC: <span id="utc"></span></h1>
	<form method="post" action="index.php">
		<label for="STATION_CALLSIGN">Station Callsign:</label>
		<input type="text" name="STATION_CALLSIGN" id="STATION_CALLSIGN" required><br>
		<label for="CALL">Call:</label>
		<input type="text" name="CALL" id="CALL" required><br>
		<label for="QSO_DATE">QSO Date:</label>
		<input type="date" name="QSO_DATE" id="QSO_DATE" required><br>
		<label for="TIME_ON">Time On:</label>
		<input type="time" name="TIME_ON" id="TIME_ON" required placeholder=""><br>
		<label for="BAND">Band:</label>
		<select name="BAND" id="BAND">
			<option value="160M">160M</option>
			<option value="80M">80M</option>
			<option value="60M">60M</option>
			<option value="40M">40M</option>
			<option value="30M">30M</option>
			<option value="20M">20M</option>
			<option value="17M">17M</option>
			<option value="15M">15M</option>
			<option value="12M">12M</option>
			<option value="10M">10M</option>
			<option value="6M">6M</option>
			<option value="4M">4M</option>
			<option value="2M">2M</option>
			<option value="70CM">70CM</option>
			<option value="23CM">23CM</option>
		</select><br>
		<label for="MODE">Mode:</label>
		<select name="MODE" id="MODE" required>
			<option value="PHONE">PHONE</option>
			<option value="SSB">SSB</option>
			<option value="CW">CW</option>
			<option value="FT8">FT8</option>
			<option value="RTTY">RTTY</option>
			<option value="PSK31">PSK31</option>
			<option value="PSK63">PSK63</option>
			<option value="PSK125">PSK125</option>
			<option value="JT65">JT65</option>
			<option value="JT9">JT9</option>
			<option value="JT4">JT4</option>
			<option value="JT6M">JT6M</option>
			<option value="JT44">JT44</option>
			<option value="FT4">FT4</option>
		</select><br>
		<label for="PARK">Park:</label>
		<input type="text" name="PARK" id="PARK" required><br>

		<input type="hidden" name="initial" value="<?php
		if (isset($_POST['submit']))
		{
			if ($_POST['initial'] == "true")
			{
				print "false";
			}
			else
			{
				print "true";
			}
		}
		else
		{
			print "true";
		}
		?>">

		<input type="submit" name="submit" value="Submit">
	</form>

	<?php
	if (isset($adif))
	{
		$adif_htmlencoded = htmlentities($adif);
		print "<pre>";
		print $adif_htmlencoded;
		print "</pre>";
	}
	?>
	<!-- Download button -->
	<?php
	if (isset($adif))
	{
		print ("<a href=\"data:text/adif;charset=utf-8," . urlencode($adif) . "\" download=\"qso.adi\">Download ADIF</a>");
	}
	?>

	<script>

		var utc = new Date().toISOString().slice(0, 19).replace('T', ' ');
		document.getElementById('utc').innerHTML = utc;

		// update every second
		setInterval(function () {
			var utc = new Date().toISOString().slice(0, 19).replace('T', ' ');
			document.getElementById('utc').innerHTML = utc;
		}, 1000);


		// validate form
		var form = document.querySelector('form');
		form.addEventListener('submit', function (event) {

			var qso_date = new Date(form.QSO_DATE.value + ' ' + form.TIME_ON.value);
			var now = new Date();
			if (qso_date > now) {
				alert('QSO Date and Time On must be in the past');
				event.preventDefault();
			}

			// Check if the callsign is valid
			var call = form.CALL.value;
			if (!call.match(/^[A-Z0-9\/]+$/)) {
				alert('Invalid callsign');
				event.preventDefault();
			}

			// submit the form
			form.submit();
		});

	</script>

</body>

</html>