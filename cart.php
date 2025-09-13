<?php
session_start();
require_once('config.php');

// Handle cart updates (remove item, change quantity)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $new_quantity = $_POST['quantity'];
        if ($new_quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - FurShield</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container py-5">
        <h2 class="section-title text-center mb-5">Your Shopping Cart</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card p-4 shadow-sm">
                        <ul class="list-group list-group-flush">
                            <?php 
                            $cart_total = 0;
                            foreach ($_SESSION['cart'] as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $cart_total += $subtotal;
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h5 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                            <small class="text-muted">$<?php echo number_format($item['price'], 2); ?> each</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <form method="POST" class="d-flex">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control me-2" style="width: 70px;">
                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary me-2">Update</button>
                                            <button type="submit" name="remove_item" class="btn btn-sm btn-danger">Remove</button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="card-footer d-flex justify-content-between align-items-center mt-4">
                            <h4>Total:</h4>
                            <h4>$<?php echo number_format($cart_total, 2); ?></h4>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <p>Your cart is empty. Start shopping now!</p>
                <a href="products.php" class="btn btn-primary">Go to Products</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>