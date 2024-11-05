<?php
session_start();

// Handle logout action
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header('Location: ../Login/login.php');
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../Login/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>About Us</title>
    <link rel="stylesheet" type="text/css" href="about.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav>
        <div class="nav-container">
            <img src="../Images/Logo.png" alt="Logo" class="logo">
            <div class="nav-links">
                <a href="../Home/home.php" class="nav-link">Home</a>
                <a href="../Questions/questions.php" class="nav-link">Questions</a>
                <a href="../About/about.php" class="nav-link">About</a>
                <a href="about.php?logout=true" class="nav-link logout">Logout</a>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="about-content">
            <h2>About Us</h2>
            <p>Wonder is a platform built for curiosity. We believe that the power of questions can unlock knowledge,
                innovation, and insight. Our mission is to create a space where people can ask questions, share their
                knowledge, and learn from each other in an open and friendly community.</p>
            <p>Founded with a passion for discovery, we strive to connect people through the questions they ask and the
                answers they share. Whether you're seeking advice, learning something new, or sharing your expertise, Wonder
                provides a platform where curiosity meets knowledge.</p>
            <p>Join us on this journey to expand our understanding of the world, one question at a time.</p>
        </div>

        <div class="about-section">
            <img src="../Images/Team.png" alt="Team Photo" class="about-image">
            <h3>Our Team</h3>
            <p>We are a diverse group of designers, developers, and enthusiasts who believe in the power of collaboration
                and shared knowledge. Our team is dedicated to creating a space where curiosity thrives and knowledge is
                always just a question away.</p>
        </div>
    </main>
</body>

</html>
