<?php
// Connect to the MySQL database (replace with your actual database credentials)
$host = "localhost";
$dbname = "kitchen";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the registration form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $comment = $_POST["comment"];

    // Check if the username is already taken
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // Username is already taken, display an error message
        echo "Username is already taken. Please choose another.";
    } else {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, password, phone, email, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $phone, $email, $comment]);

        // Redirect to the login page after successful registration
        header("Location: cart.php");
        exit();
    }
}
?>
