<?php
session_start();

// Database connection details
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'login credentials';
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Register user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $username = mysqli_real_escape_string($conn, $_POST['Username']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashedPassword')";
    if ($conn->query($sql) === TRUE) {
        echo "You are registered, " . htmlspecialchars($username) . "!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Login user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Successful login, set session variable and redirect
            $_SESSION['username'] = $row['username'];
            header("Location: welcome.php"); // Redirect to the welcome page
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No user found with that email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Register & Login</title>
</head>
<body>
    <!-- Registration Form -->
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="Email" placeholder="Enter your email" required>
        <input type="text" name="Username" placeholder="Enter your username" required>
        <input type="password" name="Password" placeholder="Enter your password" required>
        <input type="submit" name="register" value="Register">
    </form>

    <!-- Login Form -->
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="Email" placeholder="Enter your email" required>
        <input type="password" name="Password" placeholder="Enter your password" required>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>
