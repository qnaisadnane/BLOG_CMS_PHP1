<?php

$host = "localhost"; 
$dbname = "blog";
$user = "root";
$password = "";

try{
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>



















