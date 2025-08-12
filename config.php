<?php
// $host = "sql300.infinityfree.com";
// $user = "if0_39648510";
// $pass = "7wr87RUsgS7Uq";  // your MySQL password
// $db = "if0_39648510_t1";
$host = "localhost"; // Local MySQL server
$user = "root";      // Default XAMPP user
$pass = "";          // Usually empty for XAMPP
$db   = "resume_db";



$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
