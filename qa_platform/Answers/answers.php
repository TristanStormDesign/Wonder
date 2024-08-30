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

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'wonder');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch questions and their answers
$sql = "
SELECT q.question, q.username AS questioner, a.answer, a.username AS answerer 
FROM questions q 
LEFT JOIN answers a ON q.id = a.question_id 
ORDER BY q.created_at DESC, a.created_at ASC
";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Answers</title>
    <link rel="stylesheet" type="text/css" href="new-answers.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav>
        <div class="nav-container">
            <img src="../Images/Logo.png" alt="Logo" class="logo">
            <div class="nav-links">
                <a href="../Home/home.php" class="nav-link">Home</a>
                <a href="../Questions/questions.php" class="nav-link">Questions</a>
                <a href="../Answers/answers.php" class="nav-link">Answers</a>
                <a href="../About/about.php" class="nav-link">About</a>
                <a href="answers.php?logout=true" class="nav-link logout">Logout</a>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <h2>Questions & Answers</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="qa">
                    <p><strong><?php echo htmlspecialchars($row['questioner']); ?> asked:</strong>
                        <?php echo htmlspecialchars($row['question']); ?></p>
                    <?php if ($row['answer']): ?>
                        <p><strong><?php echo htmlspecialchars($row['answerer']); ?> answered:</strong>
                            <?php echo htmlspecialchars($row['answer']); ?></p>
                    <?php else: ?>
                        <p><em>No answers yet.</em></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No questions and answers available.</p>
        <?php endif; ?>
    </main>
</body>

</html>