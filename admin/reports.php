<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Only allow access for admin role
if ($_SESSION['role'] != 'admin') {
    die("❌ Access denied! Only admin can access this page.");
}

include '../config.php';

// Users by role
$users_by_role = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");

// Appointments summary
$appointments_summary = $conn->query("SELECT status, COUNT(*) as count FROM appointments GROUP BY status");

// Orders summary
$orders_summary = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");

// Products summary
$total_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$low_stock = $conn->query("SELECT COUNT(*) as c FROM products WHERE stock_quantity < 5")->fetch_assoc()['c'];

// Vet summary
$vets = $conn->query("
    SELECT u.id, u.name, u.email,
           vp.qualification, vp.specialization,
           COUNT(a.id) AS total_appointments
    FROM users u
    LEFT JOIN vet_profiles vp ON u.id = vp.vet_id
    LEFT JOIN appointments a ON u.id = a.vet_id
    WHERE u.role = 'vet'
    GROUP BY u.id, u.name, u.email, vp.qualification, vp.specialization
");

// Owner summary
$owners = $conn->query("
    SELECT u.id, u.name, u.email,
           COUNT(DISTINCT p.id) AS total_pets,
           COUNT(DISTINCT a.id) AS total_appointments,
           COUNT(DISTINCT o.id) AS total_orders
    FROM users u
    LEFT JOIN pets p ON u.id = p.owner_id
    LEFT JOIN appointments a ON u.id = a.owner_id
    LEFT JOIN orders o ON u.id = o.owner_id
    WHERE u.role = 'owner'
    GROUP BY u.id, u.name, u.email
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - System Reports</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ========== VARIABLES ========== */
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

/* ========== GLOBAL STYLES ========== */
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

/* ========== SIDEBAR ========== */
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

/* ========== MAIN CONTENT ========== */
.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 20px;
}

/* Top header */
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

/* Page title */
.page-title {
    font-size: 1.8rem;
    color: var(--royal-brown);
}

/* ========== USER PROFILE DROPDOWN ========== */
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

/* ========== REPORT CARDS ========== */
.report-section {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 10px;
    color: var(--accent);
}

/* Table styles */
.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.report-table th, 
.report-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.report-table th {
    background-color: rgba(200, 155, 123, 0.1);
    color: var(--royal-brown);
    font-weight: 600;
}

.report-table tr:hover {
    background-color: rgba(200, 155, 123, 0.05);
}

/* Stats list */
.stats-list {
    list-style: none;
    padding: 0;
}

.stats-list li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

.stats-list li:last-child {
    border-bottom: none;
}

.stats-value {
    font-weight: 600;
    color: var(--royal-brown);
}

/* ========== RESPONSIVE DESIGN ========== */
@media (max-width: 992px) {
    .sidebar { width: 80px; overflow: hidden; }
    .sidebar:hover { width: 250px; }
    .sidebar-logo span { display: none; }
    .sidebar:hover .sidebar-logo span { display: inline; }
    .sidebar-menu a span { display: none; }
    .sidebar:hover .sidebar-menu a span { display: inline; }
    .main-content { margin-left: 80px; }
    .sidebar:hover ~ .main-content { margin-left: 250px; }
}

@media (max-width: 768px) {
    .sidebar { width: 0; padding-top: 60px; }
    .sidebar.show { width: 250px; }
    .main-content { margin-left: 0; }
    .menu-toggle { display: block; }
    .report-table {
        display: block;
        overflow-x: auto;
    }
}

/* ========== MENU TOGGLE BUTTON ========== */
.menu-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: var(--royal-brown); cursor: pointer; margin-right: 15px; }
@media (max-width: 768px) { .menu-toggle { display: block; } }

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
        <li><a href="manage_products.php"><i class="fas fa-box"></i> <span>Manage Products</span></a></li>
        <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
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

    <h1 class="page-title">System Reports</h1>

    <!-- User profile -->
    <div class="user-profile">
        <button class="user-btn" id="userDropdownBtn">
            <div class="user-avatar">A</div>
            <span class="user-name">Admin</span>
            <i class="fas fa-chevron-down"></i>
        </button>

        <div class="dropdown-menu" id="userDropdown">
            <a href="profile.php" class="dropdown-item">
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

<!-- Users by Role Report -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-users"></i> Users by Role</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $users_by_role->fetch_assoc()): ?>
                <tr>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td><?= $row['count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Appointments Summary -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-calendar"></i> Appointments Summary</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $appointments_summary->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Orders Summary -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-shopping-cart"></i> Orders Summary</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $orders_summary->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Products Summary -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-box"></i> Products Summary</h2>
    <ul class="stats-list">
        <li>
            <span>Total Products:</span>
            <span class="stats-value"><?= $total_products ?></span>
        </li>
        <li>
            <span>Low Stock (&lt;5):</span>
            <span class="stats-value"><?= $low_stock ?></span>
        </li>
    </ul>
</div>

<!-- Vet Summary -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-user-md"></i> Vet Summary</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>Vet Name</th>
                <th>Email</th>
                <th>Qualification</th>
                <th>Specialization</th>
                <th>Total Appointments</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $vets->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['qualification'] ?? 'N/A' ?></td>
                    <td><?= $row['specialization'] ?? 'General' ?></td>
                    <td><?= $row['total_appointments'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Owner Summary -->
<div class="report-section">
    <h2 class="section-title"><i class="fas fa-user"></i> Owner Summary</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>Owner Name</th>
                <th>Email</th>
                <th>Total Pets</th>
                <th>Total Appointments</th>
                <th>Total Orders</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $owners->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['total_pets'] ?></td>
                    <td><?= $row['total_appointments'] ?></td>
                    <td><?= $row['total_orders'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

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
</script>

</body>
</html>