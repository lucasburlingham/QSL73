<?php

require 'db.php';

// export the log to an adif file

// get the logs
$result = $db->query('SELECT * FROM logs');

// create the adif file
$adif = "<adif_ver:5>3.0.5\n";
$adif .= "<programid:5>ADIF\n";