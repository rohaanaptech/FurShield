<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to send messages']);
    exit();
}

// Get POST data
$vet_id = $_POST['vet_id'] ?? null;
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Validate input
if (!$vet_id || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Verify the vet exists
$vet_check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'vet'");
$vet_check->bind_param("i", $vet_id);
$vet_check->execute();
$vet_result = $vet_check->get_result();

if ($vet_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Veterinarian not found']);
    exit();
}

// Insert message into database
$user_id = $_SESSION['user_id'];
$query = "INSERT INTO vet_messages (user_id, vet_id, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $user_id, $vet_id, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $stmt->error]);
}

$stmt->close();
$vet_check->close();
$conn->close();
?>