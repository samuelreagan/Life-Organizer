<?php
$host       = "*****";
$db         = "*****";
$username   = "*****";
$password   = "*****";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>