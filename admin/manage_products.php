<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    $_SESSION['message'] = "✅ Product successfully deleted!";
    header("Location: manage_products.php");
    exit();
}

// Handle product updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    // Handle image upload if provided
    $image_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/products/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = "product_" . $id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_sql = ", image='$new_filename'";
        }
    }

    $sql = "UPDATE products SET name='$name', category='$category', price=$price, stock_quantity=$stock $image_sql WHERE id=$id";
    
    if ($conn->query($sql)) {
        $_SESSION['message'] = "✅ Product updated successfully!";
    } else {
        $_SESSION['message'] = "❌ Error updating product: " . $conn->error;
    }
    
    header("Location: manage_products.php");
    exit();
}

// Get all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = [];
while($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get categories for filter
$categories_result = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = [];
while($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Handle category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Apply filters if set
$filter_condition = "";
if (!empty($category_filter) && $category_filter != 'all') {
    $filter_condition .= " AND category='".$conn->real_escape_string($category_filter)."'";
}
if (!empty($search_term)) {
    $filter_condition .= " AND (name LIKE '%".$conn->real_escape_string($search_term)."%' OR description LIKE '%".$conn->real_escape_string($search_term)."%')";
}

$filtered_result = $conn->query("SELECT * FROM products WHERE 1=1 $filter_condition ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Manage Products</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ========== CUSTOM STYLES ========== */
:root {
    --cream: #f8f4e9;
    --royal-brown: #6d4c3d;
    --accent: #c89b7b;
    --dark: #2a2a2a;
    --light: #ffffff;
    --sidebar: #2c3e50;
    --sidebar-hover: #34495e;
    --header-bg: #ffffff;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: #f5f7f9;
    color: #333;
    line-height: 1.6;
    display: flex;
    min-height: 100vh;
}

h1, h2, h3, h4, h5 {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: var(--royal-brown);
}

/* Sidebar styles */
.sidebar {
    width: 250px;
    background: var(--sidebar);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 60px;
    transition: all 0.3s ease;
    z-index: 100;
}

.sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-logo i {
    margin-right: 10px;
    color: var(--accent);
}

.sidebar-menu {
    list-style: none;
    padding: 20px 0;
}

.sidebar-menu li {
    margin-bottom: 5px;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #eee;
    text-decoration: none;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: var(--sidebar-hover);
    color: white;
    border-left: 4px solid var(--accent);
}

.sidebar-menu i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Main content area */
.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 20px;
}

/* Top header with user info */
.top-header {
    background: var(--header-bg);
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--card-shadow);
    border-radius: 8px;
    margin-bottom: 25px;
}

.page-title {
    font-size: 1.8rem;
    color: var(--royal-brown);
}

/* User profile dropdown */
.user-profile {
    position: relative;
    display: inline-block;
}

.user-btn {
    display: flex;
    align-items: center;
    background: rgba(200, 155, 123, 0.1);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    padding: 8px 15px;
    transition: all 0.3s ease;
}

.user-btn:hover {
    background: rgba(200, 155, 123, 0.2);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 10px;
}

.user-name {
    font-weight: 500;
    margin-right: 10px;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    width: 200px;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    padding: 10px 0;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    padding: 10px 20px;
    display: flex;
    align-items: center;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f5f5f5;
}

.dropdown-item i {
    margin-right: 10px;
    color: var(--accent);
}

.dropdown-divider {
    height: 1px;
    background: #eee;
    margin: 5px 0;
}

/* Filter section */
.filter-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: var(--card-shadow);
    margin-bottom: 25px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    font-weight: 500;
    margin-bottom: 5px;
    color: var(--royal-brown);
}

.filter-group select, 
.filter-group input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-family: 'Montserrat', sans-serif;
}

.filter-button {
    padding: 10px 20px;
    background: var(--royal-brown);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    align-self: flex-end;
    height: 38px;
}

.filter-button:hover {
    background: var(--accent);
}

/* Products table */
.products-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
}

.products-table th {
    background-color: var(--royal-brown);
    color: white;
    text-align: left;
    padding: 15px;
    font-weight: 600;
}

.products-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.products-table tr:last-child td {
    border-bottom: none;
}

.products-table tr:hover {
    background-color: #f9f9f9;
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}

.input-field {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: 'Montserrat', sans-serif;
}

.update-btn {
    padding: 8px 15px;
    background: var(--royal-brown);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.update-btn:hover {
    background: var(--accent);
}

.delete-btn {
    display: inline-block;
    padding: 8px 15px;
    background: #e74c3c;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-top: 5px;
}

.delete-btn:hover {
    background: #c0392b;
}

.add-product-btn {
    display: inline-block;
    padding: 12px 25px;
    background: var(--royal-brown);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.add-product-btn:hover {
    background: var(--accent);
}

/* Message alert */
.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .sidebar { 
        width: 80px; 
        overflow: hidden; 
    }
    .sidebar:hover { 
        width: 250px; 
    }
    .sidebar-logo span { 
        display: none; 
    }
    .sidebar:hover .sidebar-logo span { 
        display: inline; 
    }
    .sidebar-menu a span { 
        display: none; 
    }
    .sidebar:hover .sidebar-menu a span { 
        display: inline; 
    }
    .main-content { 
        margin-left: 80px; 
    }
    .sidebar:hover ~ .main-content { 
        margin-left: 250px; 
    }
}

@media (max-width: 768px) {
    .sidebar { 
        width: 0; 
        padding-top: 60px; 
    }
    .sidebar.show { 
        width: 250px; 
    }
    .main-content { 
        margin-left: 0; 
    }
    .menu-toggle { 
        display: block; 
    }
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-group {
        width: 100%;
    }
    .products-table {
        display: block;
        overflow-x: auto;
    }
}

/* Menu toggle button for mobile */
.menu-toggle { 
    display: none; 
    background: none; 
    border: none; 
    font-size: 1.5rem; 
    color: var(--royal-brown); 
    cursor: pointer; 
    margin-right: 15px; 
}

@media (max-width: 768px) { 
    .menu-toggle { 
        display: block; 
    } 
}

/* My custom tweaks */
.products-table tr:nth-child(even) {
    background-color: #f8f4e9;
}

.update-btn:active {
    transform: scale(0.98);
}

.input-field:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(200,155,123,0.2);
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-paw"></i>
            <span>FurShield</span>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="manage_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
        <li><a href="manage_products.php" class="active"><i class="fas fa-box"></i> <span>Manage Products</span></a></li>
        <li><a href="reports.php"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
        <li><a href="#"><i class="fas fa-calendar"></i> <span>Appointments</span></a></li>
        <li><a href="#"><i class="fas fa-store"></i> <span>Orders</span></a></li>
        <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
    </ul>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- Top header -->
<div class="top-header">
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <h1 class="page-title">Manage Products</h1>

    <!-- User profile -->
    <div class="user-profile">
        <button class="user-btn" id="userDropdownBtn">
            <div class="user-avatar">A</div>
            <span class="user-name">Admin</span>
            <i class="fas fa-chevron-down"></i>
        </button>

        <div class="dropdown-menu" id="userDropdown">
            <a href="#" class="dropdown-item">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="#" class="dropdown-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <div class="dropdown-divider"></div>
            <a href="../logout.php" class="dropdown-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Display messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert <?php echo strpos($_SESSION['message'], '✅') !== false ? 'alert-success' : 'alert-error'; ?>">
        <?php 
        echo $_SESSION['message']; 
        unset($_SESSION['message']);
        ?>
    </div>
<?php endif; ?>

<!-- Add product button -->
<a href="add_product.php" class="add-product-btn"><i class="fas fa-plus"></i> Add New Product</a>

<!-- Filter section -->
<div class="filter-section">
    <div class="filter-group">
        <label for="category">Filter by Category</label>
        <select id="category" onchange="applyFilters()">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo $category_filter == $cat ? 'selected' : ''; ?>>
                    <?php echo ucfirst($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="search">Search Products</label>
        <input type="text" id="search" placeholder="Product name or description" value="<?php echo htmlspecialchars($search_term); ?>">
    </div>
    
    <button class="filter-button" onclick="applyFilters()"><i class="fas fa-filter"></i> Apply Filters</button>
</div>

<!-- Products table -->
<table class="products-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price ($)</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($filtered_result->num_rows > 0): ?>
            <?php while($row = $filtered_result->fetch_assoc()): ?>
                <tr>
                    <form method="post" enctype="multipart/form-data">
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="../uploads/products/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
                            <?php else: ?>
                                <img src="../assets/placeholder-product.jpg" alt="No image" class="product-image">
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*" style="margin-top:5px;">
                        </td>
                        <td>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="input-field" required>
                        </td>
                        <td>
                            <input type="text" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" class="input-field" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" class="input-field" required>
                        </td>
                        <td>
                            <input type="number" name="stock" value="<?php echo $row['stock_quantity']; ?>" class="input-field" required>
                        </td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="update_product" class="update-btn"><i class="fas fa-save"></i> Update</button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px;">
                    No products found. <a href="add_product.php">Add your first product</a>.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

<!-- JS -->
<script>
// Toggle dropdown menu
document.getElementById('userDropdownBtn').addEventListener('click', function() {
    document.getElementById('userDropdown').classList.toggle('show');
});

// Close dropdown when clicking outside
window.addEventListener('click', function(event) {
    if (!event.target.matches('#userDropdownBtn') && !event.target.closest('#userDropdownBtn')) {
        document.getElementById('userDropdown').classList.remove('show');
    }
});

// Toggle sidebar for mobile
document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('show');
});

// Apply filters function
function applyFilters() {
    const category = document.getElementById('category').value;
    const search = document.getElementById('search').value;
    
    let url = 'manage_products.php?';
    if (category !== 'all') {
        url += 'category=' + encodeURIComponent(category) + '&';
    }
    if (search) {
        url += 'search=' + encodeURIComponent(search);
    }
    
    window.location.href = url;
}

// Small enhancement - highlight low stock items
document.addEventListener('DOMContentLoaded', function() {
    const stockInputs = document.querySelectorAll('input[name="stock"]');
    stockInputs.forEach(function(input) {
        if (parseInt(input.value) < 10) {
            input.style.borderColor = '#e74c3c';
            input.style.backgroundColor = '#fadbd8';
        }
    });
});
</script>

</body>
</html>