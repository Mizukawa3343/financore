<?php
require_once "./dbconn.php";
$sql = "SELECT * FROM receipts";
$stmt = $conn->prepare($sql);
$stmt->execute();
date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines

$now = new DateTime();
echo $now->format('Y-m-d H:i:s'); // Example: 2025-10-08 15:47:32
