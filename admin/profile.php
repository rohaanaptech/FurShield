<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$adminId = $_SESSION['user_id'];

// Fetch current admin data
$result = $conn->query("SELECT name, email, created_at FROM users WHERE id=$adminId");
$admin = $result->fetch_assoc();

// Handle update
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email)) {
        $message = "❌ Name and email are required!";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Please enter a valid email address!";
        $messageType = "error";
    } elseif (!empty($password) && $password != $confirm_password) {
        $message = "❌ Passwords do not match!";
        $messageType = "error";
    } elseif (!empty($password) && strlen($password) < 8) {
        $message = "❌ Password must be at least 8 characters long!";
        $messageType = "error";
    } else {
        // Check if email already exists (excluding current user)
        $emailCheck = $conn->query("SELECT id FROM users WHERE email='$email' AND id != $adminId");
        if ($emailCheck->num_rows > 0) {
            $message = "❌ Email already exists!";
            $messageType = "error";
        } else {
            if (!empty($password)) {
                // update with password
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
                $stmt->bind_param("sssi", $name, $email, $hashed, $adminId);
            } else {
                // update without password
                $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
                $stmt->bind_param("ssi", $name, $email, $adminId);
            }

            if ($stmt->execute()) {
                $message = "✅ Profile updated successfully!";
                $messageType = "success";
                
                // Refresh admin data
                $result = $conn->query("SELECT name, email, created_at FROM users WHERE id=$adminId");
                $admin = $result->fetch_assoc();
            } else {
                $message = "❌ Error updating profile: " . $stmt->error;
                $messageType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Admin Profile</title>

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

/* Profile container */
.profile-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 25px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr;
    }
}

/* Profile card */
.profile-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--card-shadow);
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    font-weight: bold;
    margin: 0 auto 20px;
}

.profile-name {
    font-size: 1.5rem;
    margin-bottom: 5px;
}

.profile-role {
    display: inline-block;
    padding: 4px 12px;
    background: var(--royal-brown);
    color: white;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.profile-stats {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
    padding: 15px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--royal-brown);
}

.stat-label {
    font-size: 0.85rem;
    color: #777;
}

.profile-date {
    color: #777;
    font-size: 0.9rem;
    margin-top: 15px;
}

/* Edit form card */
.edit-form-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--card-shadow);
}

.form-title {
    font-size: 1.4rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--royal-brown);
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-family: 'Montserrat', sans-serif;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
}

.password-note {
    font-size: 0.85rem;
    color: #777;
    margin-top: 5px;
}

.submit-btn {
    padding: 12px 25px;
    background: var(--royal-brown);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.submit-btn:hover {
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
.profile-stats {
    position: relative;
}

.profile-stats:before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 1px;
    height: 100%;
    background: #eee;
}

.form-control:invalid {
    border-color: #e74c3c;
}

.submit-btn:active {
    transform: scale(0.98);
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
        <li><a href="manage_products.php"><i class="fas fa-box"></i> <span>Manage Products</span></a></li>
        <li><a href="reports.php"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
        <li><a href="#"><i class="fas fa-calendar"></i> <span>Appointments</span></a></li>
        <li><a href="#"><i class="fas fa-store"></i> <span>Orders</span></a></li>
        <li><a href="profile.php" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
    </ul>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- Top header -->
<div class="top-header">
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <h1 class="page-title">Admin Profile</h1>

    <!-- User profile -->
    <div class="user-profile">
        <button class="user-btn" id="userDropdownBtn">
            <div class="user-avatar"><?php echo strtoupper(substr($admin['name'], 0, 1)); ?></div>
            <span class="user-name"><?php echo htmlspecialchars($admin['name']); ?></span>
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

<!-- Display messages -->
<?php if (!empty($message)): ?>
    <div class="alert <?php echo $messageType == 'success' ? 'alert-success' : 'alert-error'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Profile container -->
<div class="profile-container">
    <!-- Profile info card -->
    <div class="profile-card">
        <div class="profile-avatar"><?php echo strtoupper(substr($admin['name'], 0, 1)); ?></div>
        <h2 class="profile-name"><?php echo htmlspecialchars($admin['name']); ?></h2>
        <div class="profile-role">Administrator</div>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number">1,248</div>
                <div class="stat-label">Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">563</div>
                <div class="stat-label">Products</div>
            </div>
        </div>
        
        <div class="profile-date">
            <i class="fas fa-calendar-alt"></i> Member since <?php echo date('M j, Y', strtotime($admin['created_at'])); ?>
        </div>
    </div>
    
    <!-- Edit form card -->
    <div class="edit-form-card">
        <h2 class="form-title">Edit Profile Information</h2>
        
        <form method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                <div class="password-note">Password must be at least 8 characters long</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your new password">
            </div>
            
            <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Update Profile</button>
        </form>
    </div>
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

// Password validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        document.getElementById('confirm_password').focus();
    } else if (password && password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        document.getElementById('password').focus();
    }
});

// Show password strength
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.querySelector('.password-note');
    
    if (password.length === 0) {
        strengthText.textContent = 'Password must be at least 8 characters long';
        strengthText.style.color = '#777';
    } else if (password.length < 8) {
        strengthText.textContent = 'Password is too short';
        strengthText.style.color = '#e74c3c';
    } else {
        strengthText.textContent = 'Password strength: Good';
        strengthText.style.color = '#2ecc71';
    }
});
</script>

</body>
</html>