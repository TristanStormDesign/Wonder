<?php
session_start();

// Predefined admin credentials
$adminUsername = "Admin";
$adminPassword = "1234";

// Handle admin login
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid credentials.";
    }
}

// Handle logout action
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header('Location: admin.php');
    exit();
}

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    echo '<div id="loginPopup">
            <form method="post">
                <h2>Admin Login</h2>
                <label>Username</label>
                <input type="text" name="username" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <button type="submit" name="admin_login">Login</button>';

    if (isset($error)) {
        echo '<p style="color: red;">' . $error . '</p>';
    }

    echo '  </form>
          </div>';
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'wonder');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion of questions
if (isset($_GET['delete_question'])) {
    $question_id = (int) $_GET['delete_question'];
    $conn->query("DELETE FROM answers WHERE question_id = $question_id");
    $conn->query("DELETE FROM votes WHERE answer_id IN (SELECT id FROM answers WHERE question_id = $question_id)");
    $conn->query("DELETE FROM questions WHERE id = $question_id");
}

// Handle deletion of answers
if (isset($_GET['delete_answer'])) {
    $answer_id = (int) $_GET['delete_answer'];
    $conn->query("DELETE FROM votes WHERE answer_id = $answer_id");
    $conn->query("DELETE FROM answers WHERE id = $answer_id");
}

// Fetch questions
$questions_result = $conn->query("SELECT id, username, question, created_at FROM questions ORDER BY created_at DESC");

// Fetch answers for a specific question
function fetchAnswers($conn, $question_id)
{
    $sql = "SELECT id, username, answer, created_at FROM answers WHERE question_id = $question_id ORDER BY created_at ASC";
    return $conn->query($sql);
}

// Fetch votes for a specific answer
function fetchVotes($conn, $answer_id)
{
    $sql = "SELECT username, vote_type FROM votes WHERE answer_id = $answer_id";
    return $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>

<body>
    <nav>
        <div class="nav-container">
            <img src="../Images/Logo.png" alt="Logo" class="logo">
            <div class="nav-links">
                <a href="../Home/home.php" class="nav-link">Home</a>
                <a href="../Questions/questions.php" class="nav-link">Questions</a>
                <a href="../About/about.php" class="nav-link">About</a>
                <a href="../Admin/admin.php" class="nav-link">Admin</a>
                <a href="questions.php?logout=true" class="nav-link logout">Logout</a>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <h2>All Questions</h2>
        <?php if ($questions_result->num_rows > 0): ?>
            <ul>
                <?php while ($question = $questions_result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($question['username']); ?> Asked:</strong>
                        <?php echo htmlspecialchars($question['question']); ?>
                        <div class="actions">
                            <a href="admin.php?delete_question=<?php echo $question['id']; ?>" class="delete-btn">Delete
                                Question</a>
                        </div>
                        <div class="answers">
                            <h3>Answers:</h3>
                            <?php
                            $answers_result = fetchAnswers($conn, $question['id']);
                            if ($answers_result->num_rows > 0):
                                while ($answer = $answers_result->fetch_assoc()):
                                    ?>
                                    <div class="answer">
                                        <p><strong><?php echo htmlspecialchars($answer['username']); ?>:</strong>
                                            <?php echo htmlspecialchars($answer['answer']); ?></p>
                                        <a href="admin.php?delete_answer=<?php echo $answer['id']; ?>" class="delete-btn">Delete
                                            Answer</a>
                                        <h4>Votes:</h4>
                                        <ul>
                                            <?php
                                            $votes_result = fetchVotes($conn, $answer['id']);
                                            if ($votes_result->num_rows > 0):
                                                while ($vote = $votes_result->fetch_assoc()):
                                                    ?>
                                                    <li><?php echo htmlspecialchars($vote['username']); ?> -
                                                        <?php echo ucfirst($vote['vote_type']); ?></li>
                                                <?php endwhile; else: ?>
                                                <li>No votes yet.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endwhile; else: ?>
                                <p>No answers yet.</p>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No questions found.</p>
        <?php endif; ?>
    </main>
</body>

</html>