# Wonder QNA

**Wonder** is a web application designed to facilitate a community-driven platform where users can ask questions, share answers, and upvote or downvote contributions. This project uses PHP for backend processing and MySQL for database management, providing a seamless and interactive experience for users to seek and share knowledge.

## Features

### User Authentication
- Users can sign up and log in to their accounts.
- Login authentication is handled securely through sessions.
- Session management for persistent login status.
- Option to log out and destroy the session.

### Question Posting
- Authenticated users can post questions.
- Posted questions are stored in the database and are displayed on the platform for other users to view and answer.

### Answering Questions
- Users can browse and answer posted questions.
- Answers are tied to specific questions and displayed under them, allowing a thread of discussion to take place.

### Upvoting/Downvoting
- Users can vote on both questions and answers, improving content quality through community-driven feedback.

### Navigation
- A navigation bar allows easy access to home, questions, answers, and about sections.
- The user’s session information (username) is displayed on all pages.


## Structure

- /Images             # Contains the logo and feature images
- /Home               # Home page with form to ask questions
- /Questions          # Questions page where users can view and answer questions
- /Answers            # Answers page where users can see answers to questions
- /About              # About page with site information
- /Login              # User login page
- /Signup             # User registration page
- /Admin              # Admin page for managing questions and users


## Database Schema

The MySQL database `wonder` contains the following tables:

### Users
- `id`: INT, Primary Key, Auto Increment
- `username`: VARCHAR(255)
- `password`: VARCHAR(255)

### Questions
- `id`: INT, Primary Key, Auto Increment
- `username`: VARCHAR(255) (foreign key to users)
- `question`: TEXT
- `created_at`: TIMESTAMP

### Answers
- `id`: INT, Primary Key, Auto Increment
- `question_id`: INT (foreign key to questions)
- `username`: VARCHAR(255) (foreign key to users)
- `answer`: TEXT
- `created_at`: TIMESTAMP

## Installation

### Prerequisites
- Apache or any web server supporting PHP.
- MySQL database (or equivalent).
- PHP 7.4+.

### Steps

1. **Clone the repository:**

   ```bash
   git clone https://github.com/your-username/wonder-platform.git
   
2. **Set up the database:**

- Create a database called wonder.
- Import the SQL schema (included as wonder.sql) to create the users, questions, and answers tables.

3. **Configure the database connection:**

- In all PHP files where a database connection is required, ensure the following credentials are correct:

$conn = new mysqli('localhost', 'root', '', 'wonder');

4. **Run the server:**

- Start your Apache or web server and navigate to the project directory.

5. Visit the platform:

- Open your browser and go to http://localhost/wonder-platform.








 
