<?php

$db_host = "localhost";
$db_name = "financore_db";
$db_user = "root";
$db_password = "";

$dsn = "mysql:host=$db_host;dbname=$db_name";

try {
    $conn = new PDO($dsn, $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection error " . $e->getMessage();
}

