<?php
// logout.php
session_start(); // Start the session

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: index.php"); // Adjust this to your login page
exit();
?>
