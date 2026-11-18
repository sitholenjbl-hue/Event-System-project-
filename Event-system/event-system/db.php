<?php
// db.php  - the  PDO method
date_default_timezone_set('Africa/Johannesburg');

$host = 'localhost';
$dbname = 'event_system';
$user = 'root';      // ensures  XAMPP is a  default
$pass = '';          // XAMPP default is empty

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // creates a real prepared statements
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed.');
}


   //  (PDOException $e) {
    //  Don't show details to the users
  //  die('Database connection failed.');
  
