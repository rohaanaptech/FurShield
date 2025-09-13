<?php
session_start();
include '../config.php'; // mysqli connection ($conn)

// ----------------------
// Session & Access Check
// ----------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: login.php");
    exit();
}

$vet_id = $_SESSION['user_id'];
// ----------------------
// Fetch vet data
// ----------------------
$vet_data = [];
$stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $shelter_data = $result->fetch_assoc();
}

// ----------------------
// Fetch shelter profile data if exists
// ----------------------
$shelter_profile = [];
$profile_stmt = $conn->prepare("SELECT * FROM shelter_profiles WHERE shelter_id = ?");
$profile_stmt->bind_param("i", $shelter_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();
if ($profile_result->num_rows > 0) {
    $shelter_profile = $profile_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Shelter Profile</title>

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
    --card-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
    --hover-shadow: 0 8px 25px rgba(109, 76, 61, 0.15);
}

/* ========== GLOBAL STYLES ========== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: var(--cream);
    color: var(--royal-brown);
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
    width: 280px;
    background: linear-gradient(to bottom, var(--royal-brown), #5a3e30);
    color: var(--cream);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 30px;
    transition: all 0.3s ease;
    z-index: 100;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

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

/* ========== MAIN CONTENT ========== */
.main-content {
    flex: 1;
    margin-left: 280px;
    padding: 30px;
}

/* Top header */
.top-header {
    background: var(--light);
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--card-shadow);
    border-radius: 12px;
    margin-bottom: 30px;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.welcome-message h1 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    color: var(--royal-brown);
}

.welcome-message p {
    color: #8a7365;
    font-size: 0.95rem;
}

.date-display {
    color: #8a7365;
    font-size: 0.9rem;
}

/* ========== USER PROFILE DROPDOWN ========== */
.user-profile {
    position: relative;
    display: inline-block;
}

.user-btn {
    display: flex;
    align-items: center;
    background: rgba(109, 76, 61, 0.1);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    padding: 10px 18px;
    transition: all 0.3s ease;
}

.user-btn:hover {
    background: rgba(109, 76, 61, 0.2);
    box-shadow: 0 3px 10px rgba(109, 76, 61, 0.1);
}

.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: var(--royal-brown);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cream);
    font-weight: bold;
    margin-right: 12px;
    font-size: 1.1rem;
}

.user-name {
    font-weight: 500;
    margin-right: 10px;
    color: var(--royal-brown);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--light);
    width: 200px;
    border-radius: 12px;
    box-shadow: var(--hover-shadow);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    padding: 10px 0;
    overflow: hidden;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    padding: 12px 20px;
    display: flex;
    align-items: center;
    color: var(--royal-brown);
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: rgba(109, 76, 61, 0.05);
    padding-left: 25px;
}

.dropdown-item i {
    margin-right: 12px;
    color: var(--accent);
    width: 18px;
}

.dropdown-divider {
    height: 1px;
    background: rgba(109, 76, 61, 0.1);
    margin: 8px 0;
}

/* ========== PROFILE PAGE STYLES ========== */
.profile-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.profile-sidebar {
    background: var(--light);
    border-radius: 15px;
    padding: 30px;
    box-shadow: var(--card-shadow);
    height: fit-content;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.profile-content {
    background: var(--light);
    border-radius: 15px;
    padding: 30px;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--royal-brown), var(--accent));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: var(--cream);
    font-size: 3rem;
    position: relative;
}

.avatar-upload {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: var(--cream);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--royal-brown);
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.profile-name {
    font-size: 1.8rem;
    margin-bottom: 5px;
}

.profile-role {
    color: #8a7365;
    font-size: 1rem;
}

.profile-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin: 25px 0;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: rgba(109, 76, 61, 0.05);
    border-radius: 10px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--royal-brown);
}

.stat-label {
    font-size: 0.85rem;
    color: #8a7365;
}

.profile-menu {
    list-style: none;
    margin-top: 20px;
}

.profile-menu li {
    margin-bottom: 8px;
}

.profile-menu a {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    color: var(--royal-brown);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.profile-menu a:hover,
.profile-menu a.active {
    background: rgba(109, 76, 61, 0.05);
}

.profile-menu i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    color: var(--accent);
}

/* Form Styles */
.form-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 1.5rem;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(109, 76, 61, 0.1);
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 12px;
    color: var(--accent);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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
    border: 1px solid rgba(109, 76, 61, 0.2);
    border-radius: 8px;
    background: var(--cream);
    color: var(--royal-brown);
    font-family: 'Montserrat', sans-serif;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background: var(--royal-brown);
    color: var(--cream);
}

.btn-primary:hover {
    background: #5a3e30;
}

.btn-secondary {
    background: rgba(109, 76, 61, 0.1);
    color: var(--royal-brown);
}

.btn-secondary:hover {
    background: rgba(109, 76, 61, 0.2);
}

/* File Upload */
.file-upload {
    position: relative;
    display: inline-block;
    width: 100%;
}

.file-upload-input {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 15px;
    border: 2px dashed rgba(109, 76, 61, 0.3);
    border-radius: 8px;
    background: var(--cream);
    color: #8a7365;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-label:hover {
    border-color: var(--accent);
    color: var(--royal-brown);
}

.file-upload-label i {
    margin-right: 8px;
}

/* Working Hours */
.working-hours {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.hour-day {
    font-weight: 500;
    color: var(--royal-brown);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .sidebar {
        width: 230px;
    }
    .main-content {
        margin-left: 230px;
    }
}

@media (max-width: 992px) {
    .sidebar {
        width: 80px;
        overflow: hidden;
    }
    .sidebar:hover {
        width: 230px;
    }
    .sidebar-logo span,
    .shelter-name,
    .sidebar-menu a span {
        display: none;
    }
    .sidebar:hover .sidebar-logo span,
    .sidebar:hover .shelter-name,
    .sidebar:hover .sidebar-menu a span {
        display: block;
    }
    .main-content {
        margin-left: 80px;
    }
    .sidebar:hover ~ .main-content {
        margin-left: 230px;
    }
    .profile-container {
        grid-template-columns: 1fr;
    }
    .form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 0;
        padding-top: 60px;
        z-index: 1000;
    }
    .sidebar.show {
        width: 230px;
    }
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    .menu-toggle {
        display: block;
    }
    .top-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    .user-profile {
        align-self: flex-end;
    }
    .working-hours {
        grid-template-columns: 1fr;
    }
}
/* Message Styles */
.message-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: var(--hover-shadow);
    display: flex;
    align-items: center;
    animation: slideIn 0.3s ease;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert i {
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
/* Menu Toggle Button */
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
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: var(--light);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        justify-content: center;
    }
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
        <div class="shelter-name">Shelter Dashboard</div>
    </div>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="add_pet.php"><i class="fas fa-plus-circle"></i> <span>Add Adoptable Pet</span></a></li>
        <li><a href="my_pets.php"><i class="fas fa-paw"></i> <span>My Adoptable Pets</span></a></li>
        <li><a href="add_product.php"><i class="fas fa-cart-plus"></i> <span>Add Product</span></a></li>
        <li><a href="my_products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
        <li><a href="adoption_requests.php"><i class="fas fa-heart"></i> <span>Adoption Requests</span></a></li>
        <li><a href="profile.php" class="active"><i class="fas fa-user"></i> <span>Profile</span></a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- Top header -->
<div class="top-header">
    <div class="welcome-message">
        <h1>Shelter Profile</h1>
        <p>Manage your shelter's information and professional details</p>
    </div>

    <!-- User profile -->
    <div class="user-profile">
        <button class="user-btn" id="userDropdownBtn">
            <div class="user-avatar"><?php echo substr($_SESSION['name'] ?? 'S', 0, 1); ?></div>
            <span class="user-name"><?php echo $_SESSION['name'] ?? 'Shelter'; ?></span>
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

<div class="profile-container">
    <!-- Profile Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-home"></i>
                <div class="avatar-upload" title="Upload Image">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <h2 class="profile-name"><?php echo $shelter_data['name'] ?? 'Shelter Name'; ?></h2>
            <p class="profile-role">Animal Shelter</p>
        </div>

        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number">24</div>
                <div class="stat-label">Pets</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">156</div>
                <div class="stat-label">Adoptions</div>
            </div>
        </div>

        <ul class="profile-menu">
            <li><a href="#personal" class="active"><i class="fas fa-user-circle"></i> Personal Information</a></li>
            <li><a href="#professional"><i class="fas fa-briefcase"></i> Professional Details</a></li>
            <li><a href="#social"><i class="fas fa-share-alt"></i> Social Media</a></li>
            <li><a href="#settings"><i class="fas fa-cog"></i> Account Settings</a></li>
        </ul>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Information Section -->
             <!-- Message Container -->

            <div class="form-section" id="personal">
                <h3 class="section-title"><i class="fas fa-user-circle"></i> Personal Information</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Shelter Name *</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?php echo $shelter_data['name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo $shelter_data['email'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" 
                           value="<?php echo $shelter_profile['phone'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" 
                              rows="3"><?php echo $shelter_profile['address'] ?? ''; ?></textarea>
                </div>
            </div>

            <!-- Professional Details Section -->
            <div class="form-section" id="professional">
                <h3 class="section-title"><i class="fas fa-briefcase"></i> Professional Details</h3>
                
                <div class="form-group">
                    <label for="shelter_logo">Shelter Logo</label>
                    <div class="file-upload">
                        <input type="file" id="shelter_logo" name="shelter_logo" class="file-upload-input" accept="image/*">
                        <label for="shelter_logo" class="file-upload-label">
                            <i class="fas fa-upload"></i> Choose Logo File
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Shelter Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"
                              placeholder="Tell us about your shelter, your mission, and what makes you unique"><?php echo $shelter_profile['description'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="established_year">Year Established</label>
                        <input type="number" id="established_year" name="established_year" class="form-control" 
                               min="1900" max="<?php echo date('Y'); ?>" 
                               value="<?php echo $shelter_profile['established_year'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="shelter_type">Shelter Type</label>
                        <select id="shelter_type" name="shelter_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="rescue" <?php echo (isset($shelter_profile['shelter_type']) && $shelter_profile['shelter_type'] == 'rescue') ? 'selected' : ''; ?>>Rescue Organization</option>
                            <option value="shelter" <?php echo (isset($shelter_profile['shelter_type']) && $shelter_profile['shelter_type'] == 'shelter') ? 'selected' : ''; ?>>Animal Shelter</option>
                            <option value="sanctuary" <?php echo (isset($shelter_profile['shelter_type']) && $shelter_profile['shelter_type'] == 'sanctuary') ? 'selected' : ''; ?>>Sanctuary</option>
                            <option value="humane_society" <?php echo (isset($shelter_profile['shelter_type']) && $shelter_profile['shelter_type'] == 'humane_society') ? 'selected' : ''; ?>>Humane Society</option>
                            <option value="spca" <?php echo (isset($shelter_profile['shelter_type']) && $shelter_profile['shelter_type'] == 'spca') ? 'selected' : ''; ?>>SPCA</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Working Hours</label>
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $hours = isset($shelter_profile['working_hours']) ? json_decode($shelter_profile['working_hours'], true) : [];
                    
                    foreach ($days as $day):
                        $lowerDay = strtolower($day);
                        $open = $hours[$lowerDay]['open'] ?? '';
                        $close = $hours[$lowerDay]['close'] ?? '';
                    ?>
                    <div class="working-hours">
                        <div class="hour-day"><?php echo $day; ?></div>
                        <div>
                            <input type="time" name="hours[<?php echo $lowerDay; ?>][open]" 
                                   value="<?php echo $open; ?>" class="form-control" placeholder="Open">
                        </div>
                        <div>
                            <input type="time" name="hours[<?php echo $lowerDay; ?>][close]" 
                                   value="<?php echo $close; ?>" class="form-control" placeholder="Close">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Social Media Section -->
            <div class="form-section" id="social">
                <h3 class="section-title"><i class="fas fa-share-alt"></i> Social Media</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="facebook"><i class="fab fa-facebook" style="color: #3b5998;"></i> Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" 
                               value="<?php echo $shelter_profile['facebook'] ?? ''; ?>" placeholder="https://facebook.com/yourpage">
                    </div>
                    
                    <div class="form-group">
                        <label for="instagram"><i class="fab fa-instagram" style="color: #e4405f;"></i> Instagram</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" 
                               value="<?php echo $shelter_profile['instagram'] ?? ''; ?>" placeholder="https://instagram.com/yourprofile">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="twitter"><i class="fab fa-twitter" style="color: #1da1f2;"></i> Twitter</label>
                        <input type="url" id="twitter" name="twitter" class="form-control" 
                               value="<?php echo $shelter_profile['twitter'] ?? ''; ?>" placeholder="https://twitter.com/yourprofile">
                    </div>
                    
                    <div class="form-group">
                        <label for="website"><i class="fas fa-globe" style="color: var(--royal-brown);"></i> Website</label>
                        <input type="url" id="website" name="website" class="form-control" 
                               value="<?php echo $shelter_profile['website'] ?? ''; ?>" placeholder="https://yourshelter.com">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
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
document.querySelector('.menu-toggle')?.addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('show');
});

// File upload preview
document.getElementById('shelter_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-avatar i').style.display = 'none';
            document.querySelector('.profile-avatar').style.backgroundImage = `url(${e.target.result})`;
            document.querySelector('.profile-avatar').style.backgroundSize = 'cover';
            document.querySelector('.profile-avatar').style.backgroundPosition = 'center';
        }
        reader.readAsDataURL(file);
    }
});

// Smooth scrolling for profile menu
document.querySelectorAll('.profile-menu a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
        
        // Update active class
        document.querySelectorAll('.profile-menu a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');
    });
});


</script>

</body>
</html>