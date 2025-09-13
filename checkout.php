<?php
session_start();
require_once('config.php');

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_address = $_POST['customer_address'];
    $total_amount = $cart_total;

    // Start transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // 1. Insert into orders table
        $stmt_order = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, total_amount) VALUES (?, ?, ?, ?)");
        $stmt_order->bind_param("sssd", $customer_name, $customer_email, $customer_address, $total_amount);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        // 2. Insert into order_items and update product stock
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $product_name = $item['name'];

            // Insert item into order_items
            $stmt_item->bind_param("iisid", $order_id, $product_id, $product_name, $quantity, $price);
            $stmt_item->execute();

            // Update product stock
            $stmt_stock->bind_param("ii", $quantity, $product_id);
            $stmt_stock->execute();
        }

        $stmt_item->close();
        $stmt_stock->close();

        // Commit the transaction
        $conn->commit();

        // Clear the cart
        unset($_SESSION['cart']);
        $_SESSION['message'] = "Your order has been placed successfully!";

        // Redirect to a success page or back to the products page
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Order failed. Please try again.";
        // You could also log the error: error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - FurShield</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container py-5">
        <h2 class="section-title text-center mb-5">Checkout</h2>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card p-4 shadow-sm">
                    <form method="POST" action="checkout.php">
                        <h4 class="mb-3">Order Summary</h4>
                        <ul class="list-group mb-3">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <span class="text-muted">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total (USD)</span>
                                <strong>$<?php echo number_format($cart_total, 2); ?></strong>
                            </li>
                        </ul>

                        <h4 class="mb-3">Customer Information</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="customer_name" required>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="customer_email" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="customer_address" required rows="3"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg" type="submit" name="place_order">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>