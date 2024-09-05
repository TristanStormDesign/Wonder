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

// Handle voting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote']) && isset($_POST['answer_id'])) {
    $vote_type = $_POST['vote'];
    $answer_id = (int)$_POST['answer_id'];
    $username = $_SESSION['username'];

    // Check if the user has already voted on this answer
    $check_vote_query = "SELECT * FROM votes WHERE answer_id = '$answer_id' AND username = '$username'";
    $check_vote_result = $conn->query($check_vote_query);

    if ($check_vote_result->num_rows == 0) {
        // Insert new vote
        $sql = "INSERT INTO votes (answer_id, username, vote_type) VALUES ('$answer_id', '$username', '$vote_type')";
        $conn->query($sql);
    } else {
        // Update existing vote
        $sql = "UPDATE votes SET vote_type = '$vote_type' WHERE answer_id = '$answer_id' AND username = '$username'";
        $conn->query($sql);
    }
}

// Fetch the questions from the database
$sql = "SELECT id, username, question, created_at FROM questions ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch answers and votes for a specific question
function fetchAnswers($conn, $question_id) {
    $sql = "SELECT id, username, answer, created_at FROM answers WHERE question_id = $question_id ORDER BY created_at ASC";
    $answers_result = $conn->query($sql);
    return $answers_result;
}

function fetchVoteCounts($conn, $answer_id) {
    $upvote_count_query = "SELECT COUNT(*) FROM votes WHERE answer_id = $answer_id AND vote_type = 'upvote'";
    $downvote_count_query = "SELECT COUNT(*) FROM votes WHERE answer_id = $answer_id AND vote_type = 'downvote'";

    $upvote_result = $conn->query($upvote_count_query);
    $downvote_result = $conn->query($downvote_count_query);

    $upvote_count = $upvote_result->fetch_row()[0];
    $downvote_count = $downvote_result->fetch_row()[0];

    return [$upvote_count, $downvote_count];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Questions</title>
    <link rel="stylesheet" type="text/css" href="questions.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
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
            padding: 50px 100px;
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

        .vote-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .upvote-section, .downvote-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vote-section button {
            background-color: #FF007A;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .vote-section button:hover {
            background-color: #ff0055;
        }

        .vote-count {
            font-weight: 700;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }

        .popup button {
            background-color: #FF007A;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .popup button:hover {
            background-color: #ff0055;
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
                <a href="../Admin/admin.php" class="nav-link">Admin</a>
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
                                $answers = fetchAnswers($conn, $row['id']);
                                if ($answers->num_rows > 0):
                                    while($answer_row = $answers->fetch_assoc()):
                                        list($upvote_count, $downvote_count) = fetchVoteCounts($conn, $answer_row['id']);
                                ?>
                                    <div class="popup-answer">
                                        <strong><?php echo htmlspecialchars($answer_row['username']); ?></strong> answered: <?php echo htmlspecialchars($answer_row['answer']); ?>
                                        <div class="vote-section">
                                            <div class="upvote-section">
                                                <form method="post">
                                                    <input type="hidden" name="answer_id" value="<?php echo $answer_row['id']; ?>">
                                                    <button type="submit" name="vote" value="upvote">Upvote</button>
                                                </form>
                                                <span class="vote-count">Upvotes: <?php echo $upvote_count; ?></span>
                                            </div>
                                            <div class="downvote-section">
                                                <form method="post">
                                                    <input type="hidden" name="answer_id" value="<?php echo $answer_row['id']; ?>">
                                                    <button type="submit" name="vote" value="downvote">Downvote</button>
                                                </form>
                                                <span class="vote-count">Downvotes: <?php echo $downvote_count; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p>No answers yet.</p>
                                <?php endif; ?>
                            </div>
                            <form method="post">
                                <input type="hidden" name="question_id" value="<?php echo $row['id']; ?>">
                                <textarea name="answer" placeholder="Your answer"></textarea>
                                <button type="submit">Submit Answer</button>
                            </form>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No questions found.</p>
        <?php endif; ?>
    </main>

    <div class="popup-overlay" id="popup-overlay" onclick="closePopup()"></div>
</body>
</html>
