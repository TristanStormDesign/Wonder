<?php
session_start();

// Handle logout action
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'wonder');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer']) && isset($_POST['question_id'])) {
    $answer = $conn->real_escape_string($_POST['answer']);
    $question_id = (int)$_POST['question_id'];
    $username = $_SESSION['username'];

    $sql = "INSERT INTO answers (question_id, username, answer) VALUES ('$question_id', '$username', '$answer')";
    if ($conn->query($sql) === TRUE) {
        echo "Answer submitted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch the questions from the database
$sql = "SELECT id, username, question, created_at FROM questions ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch answers for a specific question
function fetchAnswers($conn, $question_id) {
    $sql = "SELECT username, answer, created_at FROM answers WHERE question_id = $question_id ORDER BY created_at ASC";
    $answers_result = $conn->query($sql);
    return $answers_result;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Questions</title>
    <link rel="stylesheet" type="text/css" href="questions.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Add CSS for the popup */
        .popup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 50px  100px;
            border-radius: 10px;
            overflow-y: auto;
            max-height: 80vh;
            text-align: left;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .popup-header {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            cursor: pointer;
        }

        .popup-answers {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: left;
        }

        .popup-answer {
            margin-bottom: 15px;
        }

        .popup-answer strong {
            color: #FF007A;
            font-weight: 700;
        }
    </style>
    <script>
        function openPopup(questionId) {
            var popup = document.getElementById('popup-' + questionId);
            var overlay = document.getElementById('popup-overlay');
            popup.style.display = 'block';
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Disable background scrolling
        }

        function closePopup(questionId) {
            var popup = document.getElementById('popup-' + questionId);
            var overlay = document.getElementById('popup-overlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto'; // Enable background scrolling
        }
    </script>
</head>
<body>
    <nav>
        <div class="nav-container">
            <img src="../Images/Logo.png" alt="Logo" class="logo">
            <div class="nav-links">
                <a href="../Home/home.php" class="nav-link">Home</a>
                <a href="../Questions/questions.php" class="nav-link">Questions</a>
                <a href="../About/about.php" class="nav-link">About</a>
                <a href="questions.php?logout=true" class="nav-link logout">Logout</a>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <h2>Here's what we're wondering!</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['username']); ?> Asked</strong> <?php echo htmlspecialchars($row['question']); ?>
                        <button type="button" onclick="openPopup(<?php echo $row['id']; ?>)">View Answers</button>
                        <div class="popup" id="popup-<?php echo $row['id']; ?>">
                            <span class="popup-close" onclick="closePopup(<?php echo $row['id']; ?>)">&times;</span>
                            <div class="popup-header"><?php echo htmlspecialchars($row['username']); ?> Asked:</div>
                            <p><?php echo htmlspecialchars($row['question']); ?></p>
                            <div class="popup-answers">
                                <?php
                                // Open a new connection to fetch answers
                                $conn2 = new mysqli('localhost', 'root', '', 'wonder');
                                if ($conn2->connect_error) {
                                    die("Connection failed: " . $conn2->connect_error);
                                }

                                $answers = fetchAnswers($conn2, $row['id']);
                                if ($answers->num_rows > 0):
                                    while($answer_row = $answers->fetch_assoc()): ?>
                                        <div class="popup-answer">
                                            <strong><?php echo htmlspecialchars($answer_row['username']); ?> answered:</strong>
                                            <p><?php echo htmlspecialchars($answer_row['answer']); ?></p>
                                        </div>
                                    <?php endwhile;
                                else: ?>
                                    <p>No answers yet.</p>
                                <?php endif;

                                // Close the second connection
                                $conn2->close();
                                ?>
                            </div>
                            <form method="POST" action="questions.php">
                                <input type="hidden" name="question_id" value="<?php echo $row['id']; ?>">
                                <textarea name="answer" placeholder="Your answer" required></textarea>
                                <button type="submit">Submit Answer</button>
                            </form>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No questions yet. Be the first to ask!</p>
        <?php endif; ?>
        <div id="popup-overlay" class="popup-overlay"></div>
    </main>
</body>
</html>
