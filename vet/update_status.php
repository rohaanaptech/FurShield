<?php
session_start();
include '../config.php';

// Sirf vets hi status update kar saken
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$status = $_GET['status'];

// Update status
$stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo "✅ Status updated successfully! <a href='appointments.php'>Back</a>";
} else {
    echo "❌ Error: " . $stmt->error;
}
