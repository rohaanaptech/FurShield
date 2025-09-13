<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent admin from being deleted
    $conn->query("DELETE FROM users WHERE id=$id AND role!='admin'");
    $_SESSION['message'] = "✅ User successfully deleted!";
    header("Location: manage_users.php");
    exit();
}

// Handle role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $id = intval($_POST['id']);
    $new_role = $_POST['role'];

    if ($new_role != 'admin') { // Prevent admin role assignment from UI
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si", $new_role, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "✅ Role updated successfully!";
        } else {
            $_SESSION['message'] = "❌ Error updating role: " . $stmt->error;
        }
    } else {
        $_SESSION['message'] = "⚠️ Cannot assign Admin role from here!";
    }
    
    header("Location: manage_users.php");
    exit();
}

// Get all users
$result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
if (!$result) {
    die("Query failed: " . $conn->error);
}
$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}
// Get roles for filter
$roles_result = $conn->query("SELECT DISTINCT role FROM users ORDER BY role");
$roles = [];
while($row = $roles_result->fetch_assoc()) {
    $roles[] = $row['role'];
}

// Handle role filter
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Apply filters if set
$filter_condition = "";
if (!empty($role_filter) && $role_filter != 'all') {
    $filter_condition .= " AND role='".$conn->real_escape_string($role_filter)."'";
}
if (!empty($search_term)) {
    $filter_condition .= " AND (name LIKE '%".$conn->real_escape_string($search_term)."%' OR email LIKE '%".$conn->real_escape_string($search_term)."%')";
}

$filtered_result = $conn->query("SELECT id, name, email, role, created_at FROM users WHERE 1=1 $filter_condition ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Manage Users</title>

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

/* Users table */
.users-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
}

.users-table th {
    background-color: var(--royal-brown);
    color: white;
    text-align: left;
    padding: 15px;
    font-weight: 600;
}

.users-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.users-table tr:last-child td {
    border-bottom: none;
}

.users-table tr:hover {
    background-color: #f9f9f9;
}

.user-avatar-small {
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

.input-field {
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: 'Montserrat', sans-serif;
}

.role-select {
    min-width: 120px;
}

.update-btn {
    padding: 6px 12px;
    background: var(--royal-brown);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.update-btn:hover {
    background: var(--accent);
}

.delete-btn {
    display: inline-block;
    padding: 6px 12px;
    background: #e74c3c;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.delete-btn:hover {
    background: #c0392b;
}

.protected-text {
    color: #7f8c8d;
    font-style: italic;
}

.role-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.role-admin {
    background-color: #f39c12;
    color: white;
}

.role-owner {
    background-color: #3498db;
    color: white;
}

.role-vet {
    background-color: #2ecc71;
    color: white;
}

.role-shelter {
    background-color: #9b59b6;
    color: white;
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

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
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
    .users-table {
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
.users-table tr:nth-child(even) {
    background-color: #f8f4e9;
}

.update-btn:active {
    transform: scale(0.98);
}

.role-select:focus {
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
        <li><a href="manage_users.php" class="active"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
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

    <h1 class="page-title">Manage Users</h1>

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
    <div class="alert <?php 
        if (strpos($_SESSION['message'], '✅') !== false) echo 'alert-success';
        elseif (strpos($_SESSION['message'], '❌') !== false) echo 'alert-error';
        else echo 'alert-warning';
    ?>">
        <?php 
        echo $_SESSION['message']; 
        unset($_SESSION['message']);
        ?>
    </div>
<?php endif; ?>

<!-- Filter section -->
<div class="filter-section">
    <div class="filter-group">
        <label for="role">Filter by Role</label>
        <select id="role" onchange="applyFilters()">
            <option value="all">All Roles</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role; ?>" <?php echo $role_filter == $role ? 'selected' : ''; ?>>
                    <?php echo ucfirst($role); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="search">Search Users</label>
        <input type="text" id="search" placeholder="Name or email" value="<?php echo htmlspecialchars($search_term); ?>">
    </div>
    
    <button class="filter-button" onclick="applyFilters()"><i class="fas fa-filter"></i> Apply Filters</button>
</div>

<!-- Users table -->
<table class="users-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($filtered_result->num_rows > 0): ?>
            <?php while($row = $filtered_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <div class="user-avatar-small"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <?php if ($row['role'] == 'admin'): ?>
                            <span class="role-badge role-admin">Admin</span>
                            <div class="protected-text">(Locked)</div>
                        <?php else: ?>
                            <form method="post" style="display: flex; align-items: center; gap: 8px;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="role" class="input-field role-select">
                                    <option value="owner" <?php echo $row['role']=='owner'?'selected':''; ?>>Owner</option>
                                    <option value="vet" <?php echo $row['role']=='vet'?'selected':''; ?>>Vet</option>
                                    <option value="shelter" <?php echo $row['role']=='shelter'?'selected':''; ?>>Shelter</option>
                                </select>
                                <button type="submit" name="update_role" class="update-btn"><i class="fas fa-save"></i> Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['role'] != 'admin'): ?>
                            <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                        <?php else: ?>
                            <div class="protected-text">Protected</div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px;">
                    No users found.
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
    const role = document.getElementById('role').value;
    const search = document.getElementById('search').value;
    
    let url = 'manage_users.php?';
    if (role !== 'all') {
        url += 'role=' + encodeURIComponent(role) + '&';
    }
    if (search) {
        url += 'search=' + encodeURIComponent(search);
    }
    
    window.location.href = url;
}

// Small enhancement - add color to role badges for non-admin users
document.addEventListener('DOMContentLoaded', function() {
    const roleSelects = document.querySelectorAll('select[name="role"]');
    roleSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            this.style.backgroundColor = window.getComputedStyle(selectedOption).backgroundColor;
            this.style.color = 'white';
        });
        
        // Trigger change event to set initial colors
        const event = new Event('change');
        select.dispatchEvent(event);
    });
});
</script>

</body>
</html>