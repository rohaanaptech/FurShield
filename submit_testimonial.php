<?php
// submit_testimonial.php

// Database connection
$servername = "localhost"; // change if needed
$username   = "root";      // your DB username
$password   = "";          // your DB password
$dbname     = "furshield"; // change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Sanitize and validate input
$name    = trim($_POST['name']);
$email   = trim($_POST['email']);
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);
$rating  = intval($_POST['rating']);

if (empty($name) || empty($email) || empty($subject) || empty($message) || $rating < 1 || $rating > 5) {
    die("Invalid form submission.");
}

// Prepare and bind (to prevent SQL injection)
$stmt = $conn->prepare("INSERT INTO reviews (name, email, subject, message, rating) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $name, $email, $subject, $message, $rating);

if ($stmt->execute()) {
    // Redirect back with success message
    header("Location: index.php?success=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
