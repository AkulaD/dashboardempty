<?php
$db = "localhost";
$dbuser = "paketuser";
$dbpass = "Paket@1234";
$dbname = "dashboardempty";

$conn = new mysqli($db, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>