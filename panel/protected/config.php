<?php
// Name of Application
$app = "Twitch Management Portal";

// Web directory to application installation, with beginning and trailing slash.
$basedir = "/twitch/";

// Connecting to the database
$host = "localhost";
$user = "demo";
$pass = "";
$database = "twitch";

$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
