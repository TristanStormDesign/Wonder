<?php
session_start(); // Start the session

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'wonder');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Initialize the message variable
$messageType = ""; // Initialize the message type variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $password) {
            // Set session variable
            $_SESSION['username'] = $username;
            // Redirect to home page
            header("Location: ../Home/home.php");
            exit();
        } else {
            $message = "Invalid password!";
            $messageType = "error-message"; // Error message
        }
    } else {
        $message = "No user found with this username!";
        $messageType = "error-message"; // Error message
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="form-container">
        <img src="../Images/Logo.png" alt="Logo" style="margin-bottom: 40px;">
        <form method="post" action="login.php">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="signup.php" class="toggle-link">Don't have an account? <span>Sign up here</span></a>
    </div>
</body>

</html>
