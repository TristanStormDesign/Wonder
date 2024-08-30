<?php
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

    // Check if username already exists
    $checkUser = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($checkUser);

    if ($result->num_rows > 0) {
        $message = "Username already exists!";
        $messageType = "error-message"; // Error message
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful!";
            $messageType = "success-message"; // Success message
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $messageType = "error-message"; // Error message
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="form-container">
        <img src="../Images/Logo.png" alt="Logo" style="margin-bottom: 40px;">
        <form method="post" action="signup.php">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <a href="login.php" class="toggle-link">Already have an account? <span>Log in here</span></a>
    </div>
</body>

</html>
