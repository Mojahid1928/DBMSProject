<?php
$host = 'localhost';       // Your database host (usually localhost)
$dbname = 'library';       // The name of your database
$username = 'root';        // The username to connect to the database (typically 'root' for XAMPP)
$password = '';            // The password to connect to the database (leave empty for XAMPP default)

try {
    // Create a new PDO instance and set the connection to the MySQL database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception to catch errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // If connection fails, display an error message
    die("Connection failed: " . $e->getMessage());
}
?>


