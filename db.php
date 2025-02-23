<?php

// Create a new SQLite3 database if it doesn't exist
$db = new SQLite3('callsigns.sqlite');

// Create a table to store the logs
$db->exec('CREATE TABLE IF NOT EXISTS logs (callsign TEXT, grid TEXT, curr_date TEXT)');

// Create a table if it doesn't exist to store callsigns (should already exist, but check anyways)
$db->exec('CREATE TABLE IF NOT EXISTS callsigns (callsign TEXT, fullname TEXT, address TEXT)');

// Close the database
$db->close();
