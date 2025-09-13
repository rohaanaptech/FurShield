<?php
session_start();
require_once('../config.php');

// Check if the user is a shelter and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

$shelter_id = $_SESSION['user_id'];
$products = [];

// Prepare the SQL query to fetch products for the logged-in shelter
$stmt = $conn->prepare("SELECT id, name, category, price, stock_quantity FROM products WHERE shelter_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - Shelter Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ====================
           CSS Variables
           ==================== */
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
            --light-accent: #e7d8cc;
            --card-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            --hover-shadow: 0 8px 25px rgba(109, 76, 61, 0.15);
        }

        /* ====================
           Base Styles
           ==================== */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--cream);
            color: var(--royal-brown);
            margin: 0;
            padding: 0;
        }
        
        /* ====================
           Layout
           ==================== */
        .main-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(to bottom, var(--royal-brown), #5a3e30);
            color: var(--cream);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 30px;
            z-index: 100;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .content {
            flex-grow: 1;
            padding: 40px;
            background-color: var(--cream);
            /* Add margin to prevent content from going under the fixed sidebar */
            margin-left: 280px; 
        }
        
        /* ====================
           Sidebar Styles
           ==================== */
        .sidebar-header {
            padding: 0 25px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(200, 155, 123, 0.3);
            margin-bottom: 20px;
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

        .shelter-name {
            margin-top: 15px;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(248, 244, 233, 0.9);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(200, 155, 123, 0.2);
            color: var(--cream);
            transform: translateX(5px);
        }

        .sidebar-menu i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(200, 155, 123, 0.3);
        }

        .sidebar-footer a {
            color: rgba(248, 244, 233, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-footer a:hover {
            color: var(--accent);
        }

        .sidebar-footer i {
            margin-right: 8px;
        }

        /* ====================
           Content Styles
           ==================== */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-family: 'Playfair Display', serif;
            color: var(--royal-brown);
        }

        .table-container {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .table th, .table td {
            vertical-align: middle;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: var(--light-accent);
        }

        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
        }

    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-paw"></i>
                    <span>FurShield</span>
                </div>
                <div class="shelter-name">Shelter Dashboard</div>
            </div>
        
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="add_pet.php"><i class="fas fa-plus-circle"></i> <span>Add Adoptable Pet</span></a></li>
                <li><a href="my_pets.php"><i class="fas fa-paw"></i> <span>My Adoptable Pets</span></a></li>
                <li><a href="add_product.php"><i class="fas fa-cart-plus"></i> <span>Add Product</span></a></li>
                <li><a href="my_products.php" class="active"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                <li><a href="shelter_request.php"><i class="fas fa-heart"></i> <span>Adoption Requests</span></a></li>
                <li><a href="reports.php"><i class="fas fa-chart-line"></i> <span>Reports</span></a></li>
            </ul>
        
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <div class="content">
            <div class="dashboard-header">
                <h1>My Products</h1>
                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add New Product</a>
            </div>
            
            <div class="table-container">
                <?php if (count($products) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                        <td>$<?php echo number_format($product['price'] ?? 0, 2); ?></td>
                                        <td>
                                            <?php 
                                                if ($product['stock_quantity'] > 10) {
                                                    echo '<span class="badge bg-success">In Stock</span>';
                                                } elseif ($product['stock_quantity'] > 0) {
                                                    echo '<span class="badge bg-warning text-dark">Low Stock</span>';
                                                } else {
                                                    echo '<span class="badge bg-danger">Out of Stock</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <!-- <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-edit"></i> Edit</a> -->
                                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        You haven't added any products yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>