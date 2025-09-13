<?php
session_start();
include '../config.php';

if ($_SESSION['role'] != 'shelter') {
    die("❌ Access denied!");
}

$shelterId = $_SESSION['user_id'];
$result = $conn->query("SELECT name, email FROM users WHERE id=$shelterId");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $hashed, $shelterId);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $shelterId);
    }

    if ($stmt->execute()) {
        echo "✅ Profile updated!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<h2>👤 Shelter Profile</h2>
<form method="post">
  <label>Name:</label><br>
  <input type="text" name="name" value="<?= $user['name'] ?>" required><br><br>

  <label>Email:</label><br>
  <input type="email" name="email" value="<?= $user['email'] ?>" required><br><br>

  <label>New Password (leave blank if no change):</label><br>
  <input type="password" name="password"><br><br>

  <button type="submit">Update Profile</button>
</form>
