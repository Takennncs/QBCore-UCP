<?php
$steamauth['apikey'] = "";
$steamauth['domainname'] = "http://localhost";
$steamauth['loginpage'] = "dashboard.php";
$steamauth['logoutpage'] = "index.php";

$host = "localhost";
$dbname = "qbcore"; 
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Andmebaasiga ei saanud ühendust: " . $e->getMessage());
}


?>