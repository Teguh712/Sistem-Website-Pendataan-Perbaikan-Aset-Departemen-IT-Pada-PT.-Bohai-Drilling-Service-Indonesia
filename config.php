<?php
// File: config.php
$host = 'localhost';
$db = 'db_aset_itt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
}
