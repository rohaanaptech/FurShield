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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Admin Dashboard</title>

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

/* ========== STAT CARDS ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: var(--card-shadow);
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 15px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--royal-brown);
}

.stat-label {
    color: #777;
    font-size: 0.9rem;
}

/* ========== ADMIN MENU CARDS ========== */
.admin-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.menu-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.menu-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.menu-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: rgba(200,155,123,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 1.5rem;
    margin-right: 15px;
}

.menu-title {
    font-size: 1.3rem;
    color: var(--royal-brown);
}

.menu-description {
    color: #777;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.menu-link {
    display: inline-block;
    padding: 10px 20px;
    background: var(--royal-brown);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.menu-link:hover {
    background: var(--accent);
}

/* ========== RECENT ACTIVITY ========== */
.recent-activity {
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
}

.activity-list {
    list-style: none;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f5f5f5;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(200,155,123,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    margin-right: 15px;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    margin-bottom: 5px;
}

.activity-time {
    font-size: 0.8rem;
    color: #999;
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
    .stats-grid, .admin-menu { grid-template-columns: 1fr; }
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
        <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="manage_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
        <li><a href="manage_products.php"><i class="fas fa-box"></i> <span>Manage Products</span></a></li>
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

    <h1 class="page-title">Admin Dashboard</h1>

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

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(52,152,219,0.1); color:#3498db;"><i class="fas fa-users"></i></div>
        <div class="stat-content"><div class="stat-number">1,248</div><div class="stat-label">Total Users</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(46,204,113,0.1); color:#2ecc71;"><i class="fas fa-box"></i></div>
        <div class="stat-content"><div class="stat-number">563</div><div class="stat-label">Active Products</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(155,89,182,0.1); color:#9b59b6;"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-content"><div class="stat-number">327</div><div class="stat-label">Appointments</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(241,196,15,0.1); color:#f1c40f;"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-content"><div class="stat-number">892</div><div class="stat-label">Total Orders</div></div>
    </div>
</div>

<!-- Admin menu cards -->
<div class="admin-menu">
    <div class="menu-card">
        <div class="menu-header"><div class="menu-icon"><i class="fas fa-users"></i></div><h3 class="menu-title">Manage Users</h3></div>
        <p class="menu-description">View, edit, and manage user accounts and permissions for your platform.</p>
        <a href="manage_users.php" class="menu-link">Manage Users</a>
    </div>
    <div class="menu-card">
        <div class="menu-header"><div class="menu-icon"><i class="fas fa-box"></i></div><h3 class="menu-title">Manage Products</h3></div>
        <p class="menu-description">Add, edit, or remove products from your store and manage inventory.</p>
        <a href="manage_products.php" class="menu-link">Manage Products</a>
    </div>
    <div class="menu-card">
        <div class="menu-header"><div class="menu-icon"><i class="fas fa-chart-bar"></i></div><h3 class="menu-title">Reports & Analytics</h3></div>
        <p class="menu-description">View platform statistics, generate reports, and analyze performance.</p>
        <a href="reports.php" class="menu-link">View Reports</a>
    </div>
</div>

<!-- Recent Activity -->
<div class="recent-activity">
<h2 class="section-title">Recent Activity</h2>
<ul class="activity-list">
<li class="activity-item"><div class="activity-icon"><i class="fas fa-user-plus"></i></div><div class="activity-content"><p><strong>New user registration</strong> - John Doe signed up</p><span class="activity-time">2 hours ago</span></div></li>
<li class="activity-item"><div class="activity-icon"><i class="fas fa-shopping-cart"></i></div><div class="activity-content"><p><strong>New order</strong> - Order #3245 placed</p><span class="activity-time">5 hours ago</span></div></li>
<li class="activity-item"><div class="activity-icon"><i class="fas fa-box"></i></div><div class="activity-content"><p><strong>Product update</strong> - Dog Food inventory updated</p><span class="activity-time">Yesterday</span></div></li>
<li class="activity-item"><div class="activity-icon"><i class="fas fa-calendar"></i></div><div class="activity-content"><p><strong>Appointment scheduled</strong> - Dr. Smith, 3:00 PM tomorrow</p><span class="activity-time">2 days ago</span></div></li>
</ul>
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
