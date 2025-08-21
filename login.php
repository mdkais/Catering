<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kitchen"; // Use the name of your database

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user input from the form
$username = $_POST['username'];
$password = $_POST['password'];

// Query the database to check if the user exists
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

// Check if a matching user was found
if ($result->num_rows == 1) {
    // Login successful
    echo "Login successful!";
    header("Location: cart.php");
} else {
    // Login failed
    echo "Login failed. Please check your username and password.";
}

// Close the database connection
$conn->close();
?>
