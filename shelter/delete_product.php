<?php
session_start();
require_once('../config.php');

// Check if the user is a shelter and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    // Redirect to login page if not authenticated
    header("Location: ../login.php");
    exit();
}

// Check if a product ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Product ID not specified.";
    header("Location: my_products.php");
    exit();
}

$product_id = $_GET['id'];
$shelter_id = $_SESSION['user_id'];

// Prepare a statement to check ownership of the product
$check_stmt = $conn->prepare("SELECT shelter_id FROM products WHERE id = ?");
$check_stmt->bind_param("i", $product_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Product not found.";
    header("Location: my_products.php");
    exit();
}

$product = $result->fetch_assoc();
$check_stmt->close();

// Verify that the logged-in shelter owns this product to prevent unauthorized deletion
if ($product['shelter_id'] != $shelter_id) {
    $_SESSION['error'] = "You do not have permission to delete this product.";
    header("Location: my_products.php");
    exit();
}

// Prepare and execute the deletion query
$delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND shelter_id = ?");
$delete_stmt->bind_param("ii", $product_id, $shelter_id);

if ($delete_stmt->execute()) {
    $_SESSION['message'] = "Product deleted successfully.";
} else {
    $_SESSION['error'] = "Error deleting product: " . $conn->error;
}

$delete_stmt->close();
$conn->close();

// Redirect back to the products list page
header("Location: my_products.php");
exit();
?>