<?php
session_start();

// Include database connection
require_once('config.php');
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}
// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get product details from the database
    $stmt = $conn->prepare("SELECT id, name, price, stock_quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        if ($quantity > 0 && $quantity <= $product['stock_quantity']) {
            // Check if product is already in cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
            // Use a session flash message for success
            $_SESSION['message'] = "Product added to cart!";
        } else {
            $_SESSION['error'] = "Invalid quantity or out of stock!";
        }
    }
}
// Build base query for products (rest of your original code remains)
$sql = "SELECT * FROM products WHERE 1=1";
//... (The rest of your original filtering and sorting code)
// Build base query for products
$sql = "SELECT * FROM products WHERE 1=1";

// Apply filters if provided
if (!empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $sql .= " AND category = '$category'";
}

if (!empty($_GET['price_range'])) {
    $price_range = $_GET['price_range'];
    if ($price_range == "0-25") {
        $sql .= " AND price >= 0 AND price <= 25";
    } elseif ($price_range == "25-50") {
        $sql .= " AND price > 25 AND price <= 50";
    } elseif ($price_range == "50-100") {
        $sql .= " AND price > 50 AND price <= 100";
    } elseif ($price_range == "100+") {
        $sql .= " AND price > 100";
    }
}

if (!empty($_GET['stock_status'])) {
    $stock_status = $_GET['stock_status'];
    if ($stock_status == "in_stock") {
        $sql .= " AND stock_quantity > 10";
    } elseif ($stock_status == "low_stock") {
        $sql .= " AND stock_quantity > 0 AND stock_quantity <= 10";
    } elseif ($stock_status == "out_of_stock") {
        $sql .= " AND stock_quantity = 0";
    }
}

// Add ordering
$order_by = "name";
if (!empty($_GET['sort_by'])) {
    $sort_by = $_GET['sort_by'];
    if ($sort_by == "price_low_high") {
        $order_by = "price ASC";
    } elseif ($sort_by == "price_high_low") {
        $order_by = "price DESC";
    } elseif ($sort_by == "name") {
        $order_by = "name ASC";
    } elseif ($sort_by == "newest") {
        $order_by = "id DESC";
    }
}
$sql .= " ORDER BY $order_by";

// Run query
$result = $conn->query($sql);
$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Products - FurShield Premium Pet Care</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
            --light-accent: #e7d8cc;
            --medium-brown: #8a7365;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--cream);

            color: var(--royal-brown);
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--royal-brown);
        }
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #e7d8cc, #8a7365); /* gradient like example */
  color: #fff;
  padding: 100px 0;
  position: relative;
}

.hero-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap; /* responsive */
  gap: 40px;
}

.hero-text {
  flex: 1 1 500px;
}

.hero-text h1 {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
}

.hero-text h1 span {
  color: #fff;
  font-weight: 800;
}

.hero-text p {
  margin: 20px 0;
  font-size: 1.1rem;
  color: #f0f0f0;
}

.hero-buttons {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.btn-primary,
.btn-secondary {
  display: inline-block;
  padding: 12px 28px;
  border-radius: 30px;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-primary {
  background: #fff;
  color: #8a7365;
}

.btn-primary:hover {
  background: #f0f0f0;
}

.btn-secondary {
  border: 2px solid #fff;
  color: #fff;
}

.btn-secondary:hover {
  background: rgba(255,255,255,0.2);
}

.hero-image {
  flex: 1 1 500px;   /* take more space */
  text-align: center;
}

.hero-image img {
  max-width: 120%;   /* bigger than container */
  height: auto;
  border-radius: 12px;
  transform: scale(1.1); /* slight zoom effect */
}
        /* Products Section */
        .products-section {
            padding: 40px 0 80px;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }

        .product-filters {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            margin-bottom: 40px;
        }

        .filter-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--royal-brown);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .product-card {
            background-color: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(109, 76, 61, 0.08);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.12);
        }

        .product-image {
            height: 220px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--royal-brown);
        }

        .product-category {
            color: var(--medium-brown);
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .product-description {
            margin-bottom: 20px;
            color: var(--medium-brown);
            line-height: 1.6;
            flex-grow: 1;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--royal-brown);
            margin-bottom: 15px;
        }

        .product-stock {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-accent);
        }

        .stock-info {
            font-size: 0.9rem;
        }

        .in-stock {
            color: #28a745;
            font-weight: 600;
        }

        .low-stock {
            color: #ffc107;
            font-weight: 600;
        }

        .out-of-stock {
            color: #dc3545;
            font-weight: 600;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid var(--light-accent);
            border-radius: 30px;
            overflow: hidden;
        }

        .quantity-btn {
            background: none;
            border: none;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--royal-brown);
            font-weight: bold;
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            border: none;
            background: transparent;
            color: var(--royal-brown);
            font-weight: 600;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }

        .page-link {
            color: var(--royal-brown);
            border: 1px solid var(--light-accent);
            padding: 10px 18px;
            margin: 0 5px;
            border-radius: 10px;
        }

        .page-link:hover {
            background-color: var(--royal-brown);
            color: var(--cream);
            border-color: var(--royal-brown);
        }

        .page-item.active .page-link {
            background-color: var(--royal-brown);
            border-color: var(--royal-brown);
            color: var(--cream);
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(to right, rgba(109, 76, 61, 0.9), rgba(200, 155, 123, 0.85)), url('https://images.unsplash.com/photo-1591946614720-90a587da4a36?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            color: var(--cream);
            border-radius: 40px;
            margin: 40px 0;
            text-align: center;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: var(--cream);
        }

        .cta-text {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.8;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-light {
            background-color: var(--cream);
            color: var(--royal-brown);
            border: 2px solid var(--cream);
            box-shadow: 0 4px 15px rgba(248, 244, 233, 0.3);
        }

        .btn-light:hover {
            background-color: transparent;
            color: var(--cream);
            box-shadow: none;
        }

        .btn-outline-light {
            border: 2px solid var(--cream);
            color: var(--cream);
            background-color: transparent;
        }

        .btn-outline-light:hover {
            background-color: var(--cream);
            color: var(--royal-brown);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .page-title {
                font-size: 2.8rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta-title {
                font-size: 2.2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .quantity-selector {
                width: 100%;
                justify-content: center;
            }
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .toast {
            background-color: var(--royal-brown);
            color: var(--cream);
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        }

        .toast i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<!-- Include Header -->
<?php include('header.php'); ?>

<!-- Page Header -->
<!-- Hero Section -->
<section class="hero-section">
  <div class="container hero-content">
    <div class="hero-text">
      <h1>We Care <br><span>Your Pets</span></h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.</p>
      <div class="hero-buttons">
        <a href="contact.php" class="btn-primary">Contact Us</a>
        <a href="products.php" class="btn-secondary">Our Products</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="images/bgproduct.png"  alt="Pet Image">
    </div>
  </div>
</section>


<!-- Products Section -->

<section class="products-section">
    <div class="container">
        <h2 class="section-title">Shop Our Products</h2>
        
        <!-- Filters -->
        <div class="product-filters">
            <h3 class="filter-title">Find the Perfect Products for Your Pet</h3>
              <?php if (!empty($message)): ?>
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <?php
                            // Get unique categories from database
                            $category_sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category";
                            $category_result = $conn->query($category_sql);
                            while($category = $category_result->fetch_assoc()) {
                                $selected = (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($category['category']) . '" ' . $selected . '>' . htmlspecialchars($category['category']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="price_range">
                            <option value="">Any Price</option>
                            <option value="0-25" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '0-25') ? 'selected' : ''; ?>>$0 - $25</option>
                            <option value="25-50" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '25-50') ? 'selected' : ''; ?>>$25 - $50</option>
                            <option value="50-100" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '50-100') ? 'selected' : ''; ?>>$50 - $100</option>
                            <option value="100+" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] == '100+') ? 'selected' : ''; ?>>$100+</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="stock_status">
                            <option value="">Any Stock Status</option>
                            <option value="in_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] == 'in_stock') ? 'selected' : ''; ?>>In Stock</option>
                            <option value="low_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] == 'low_stock') ? 'selected' : ''; ?>>Low Stock</option>
                            <option value="out_of_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] == 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select class="form-select" name="sort_by">
                            <option value="name" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'name') ? 'selected' : ''; ?>>Sort by Name</option>
                            <option value="price_low_high" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_low_high') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high_low" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_high_low') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="newest" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Products Grid -->
        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1591946614720-90a587da4a36?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                            <p class="product-description"><?php echo !empty($product['description']) ? htmlspecialchars(substr($product['description'], 0, 100)) . '...' : 'High-quality pet product.'; ?></p>
                            
                            <div class="product-stock">
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                <div class="stock-info">
                                    <?php if ($product['stock_quantity'] > 10): ?>
                                        <span class="in-stock"><i class="fas fa-check-circle"></i> In Stock</span>
                                    <?php elseif ($product['stock_quantity'] > 0): ?>
                                        <span class="low-stock"><i class="fas fa-exclamation-circle"></i> Low Stock</span>
                                    <?php else: ?>
                                        <span class="out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn decrease-btn" data-product-id="<?php echo $product['id']; ?>">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" data-product-id="<?php echo $product['id']; ?>">
                                    <button type="button" class="quantity-btn increase-btn" data-product-id="<?php echo $product['id']; ?>">+</button>
                                </div>
                               <form method="POST" action="products.php">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="add_to_cart" value="1">
    <div class="product-actions">
        <div class="quantity-selector">
            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
        </div>
        <button type="submit" class="btn btn-primary add-to-cart-btn" <?php echo $product['stock_quantity'] == 0 ? 'disabled' : ''; ?>>
            <i class="fas fa-shopping-cart"></i> Add to Cart
        </button>
    </div>
</form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3>No products found.</h3>
                    <p>Please try different filters or check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination - Only show if there are more than 8 products -->
        <?php if (count($products) > 8): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Need Help Choosing the Right Products?</h2>
            <p class="cta-text">Our pet care experts are here to help you select the best products for your furry friend. Contact us for personalized recommendations.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-light">Contact Us</a>
                <a href="#" class="btn btn-outline-light">View Recommendations</a>
            </div>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Quantity selector functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Increase quantity
        document.querySelectorAll('.increase-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const max = parseInt(input.getAttribute('max'));
                let value = parseInt(input.value);
                
                if (value < max) {
                    input.value = value + 1;
                }
            });
        });
        
        // Decrease quantity
        document.querySelectorAll('.decrease-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                let value = parseInt(input.value);
                
                if (value > 1) {
                    input.value = value - 1;
                }
            });
        });
        
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const quantityInput = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const quantity = parseInt(quantityInput.value);
                
                // In a real application, you would send this data to your server
                // For now, we'll just show a success message
                showToast(`Added ${quantity} ${productName} to cart!`);
            });
        });
        
        // Show toast notification
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            
            document.getElementById('toastContainer').appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Animation for product cards on scroll
        const productCards = document.querySelectorAll('.product-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        productCards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(50px)';
            card.style.transition = 'all 0.5s ease';
            observer.observe(card);
        });
    });
</script>
</body>
</html>