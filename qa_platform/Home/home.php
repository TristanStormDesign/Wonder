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

// Handle question submission
if (isset($_POST['submit_question'])) {
    $username = $_SESSION['username'];
    $question = $_POST['question'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'wonder');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the question into the database
    $sql = "INSERT INTO questions (username, question) VALUES ('$username', '$question')";
    if ($conn->query($sql) === TRUE) {
        $message = "Your question has been posted!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="new-home.css">
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
                <a href="home.php?logout=true" class="nav-link logout">Logout</a>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="question-form">
            <h2>What were you wondering?</h2>
            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="post" action="home.php">
                <textarea name="question" placeholder="Ask your question..." required></textarea>
                <button type="submit" name="submit_question">Submit</button>
            </form>
        </div>
    </main>
    <!-- Feature Blocks -->
    <div class="feature-blocks">
        <div class="feature-block" id="one">
            <img src="../Images/1.png" alt="Ask Questions Icon" class="feature-icon">
            <h3>Ask Questions</h3>
            <p>Submit your inquiries and get answers from our community. Whether you're curious about a specific topic
                or need help with a problem, simply post your question, and other users will provide their insights and
                solutions.</p>
        </div>
        <div class="feature-block" id="two">
            <img src="../Images/2.png" alt="Answer Questions Icon" class="feature-icon">
            <h3>Answer Questions</h3>
            <p>Share your knowledge and help others by answering questions. Browse through the questions posted by other
                users, and offer your expertise to provide valuable information and guidance.</p>
        </div>
        <div class="feature-block" id="three">
            <img src="../Images/3.png" alt="Upvote or Downvote Icon" class="feature-icon">
            <h3>Upvote or Downvote</h3>
            <p>Help improve the quality of content by voting on questions and answers. If you find a question or answer
                helpful, upvote it to increase its visibility. Conversely, if something isn't useful, downvote it to let
                others know. Your votes help curate the best content for everyone.</p>
        </div>
    </div>
</body>

</html>