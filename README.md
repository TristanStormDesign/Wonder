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

├── /Images             # Contains the logo and feature images
├── /Home               # Home page with form to ask questions
├── /Questions          # Questions page where users can view and answer questions
├── /Answers            # Answers page where users can see answers to questions
├── /About              # About page with site information
├── /Login              # User login page
├── /Signup             # User registration page
└── /Admin              # Admin page for managing questions and users

Database Schema
The MySQL database wonder contains the following tables:

Users:

id: INT, Primary Key, Auto Increment
username: VARCHAR(255)
password: VARCHAR(255)
Questions:

id: INT, Primary Key, Auto Increment
username: VARCHAR(255) (foreign key to users)
question: TEXT
created_at: TIMESTAMP
Answers:

id: INT, Primary Key, Auto Increment
question_id: INT (foreign key to questions)
username: VARCHAR(255) (foreign key to users)
answer: TEXT
created_at: TIMESTAMP
Installation
Prerequisites
Apache or any web server supporting PHP.
MySQL database (or equivalent).
PHP 7.4+.
Steps
Clone the repository:

bash
Copy code
git clone https://github.com/your-username/wonder-platform.git
Set up the database:

Create a database called wonder.
Import the SQL schema (included as wonder.sql) to create the users, questions, and answers tables.
Configure the database connection:

In all PHP files where a database connection is required, ensure the following credentials are correct:
php
Copy code
$conn = new mysqli('localhost', 'root', '', 'wonder');
Run the server:

Start your Apache or web server and navigate to the project directory.
Visit the platform:

Open your browser and go to http://localhost/wonder-platform.
Usage
Register and Log In:

Use the signup form to register a new account.
Log in using your credentials.
Ask Questions:

Once logged in, navigate to the home page and ask a question using the form provided.
Answer Questions:

Browse existing questions and submit answers by visiting the answers page.
Log Out:

Click the "Logout" button on the navigation bar to end your session.
Security Considerations
SQL Injection: This platform currently uses real_escape_string to handle user input. Consider using prepared statements for enhanced security.
Password Storage: Passwords are stored in plain text in the database. It's recommended to implement password hashing using password_hash() and password_verify() for secure password storage.
Contributing
Contributions are welcome! Please follow these steps:

Fork the repository.
Create a new branch (git checkout -b feature-branch).
Commit your changes (git commit -m 'Add new feature').
Push to the branch (git push origin feature-branch).
Open a Pull Request.
License
This project is licensed under the MIT License.
 
