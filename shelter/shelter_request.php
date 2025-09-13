<?php
session_start();
include '../config.php';
include '../auth.php';

// ----------------------
// Session & Access Check
// ----------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../login.php");
    exit();
}

$shelter_id = $_SESSION['user_id'];

// Fetch shelter pets requests
$sql = "SELECT * FROM pets WHERE shelter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $shelter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ----------------------
// Helper function to get counts
// ----------------------
function getCount($conn, $query, $types = '', $params = []) {
    $stmt = $conn->prepare($query);
    if (!$stmt) die("Prepare failed: (" . $conn->errno . ") " . $conn->error);

    if ($types && $params) $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

// ----------------------
// Dashboard Stats
// ----------------------
$pets_count = getCount($conn, "SELECT COUNT(*) AS count FROM pets WHERE shelter_id = ?", "i", [$shelter_id]);

$products_count = getCount($conn, "SELECT COUNT(*) AS count FROM products WHERE shelter_id = ?", "i", [$shelter_id]);

$adoptions_count = getCount($conn, "SELECT COUNT(*) AS count FROM adoptions WHERE shelter_id = ? AND status = 'approved'", "i", [$shelter_id]);

$pending_requests = getCount($conn, "SELECT COUNT(*) AS count FROM adoptions WHERE shelter_id = ? AND status = 'pending'", "i", [$shelter_id]);

// ----------------------
// Recent Adoption Requests (last 5)
// ----------------------
$stmt = $conn->prepare("
    SELECT a.id, a.status, a.request_date, p.name AS pet_name, u.name AS user_name
    FROM adoptions a
    JOIN pets p ON a.pet_id = p.id
    JOIN users u ON a.user_id = u.id
    WHERE a.shelter_id = ?
    ORDER BY a.request_date DESC
    LIMIT 5
");

if (!$stmt) die("Prepare failed: (" . $conn->errno . ") " . $conn->error);

$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$recent_requests = $stmt->get_result();

// ----------------------
// Recently Added Pets (last 4)
// ----------------------
$stmt = $conn->prepare("
    SELECT id, name AS pet_name, species, breed, age, gender, notes, photo
    FROM pets
    WHERE shelter_id = ?
    ORDER BY id DESC
    LIMIT 4
");

if (!$stmt) die("Prepare failed: (" . $conn->errno . ") " . $conn->error);

$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$recent_pets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurShield - Shelter Adoption Requests</title>

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

/* ========== STATS CARDS ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--light);
    border-radius: 15px;
    padding: 25px;
    box-shadow: var(--card-shadow);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: var(--royal-brown);
}

.stat-card:nth-child(2)::before {
    background: var(--accent);
}

.stat-card:nth-child(3)::before {
    background: #8a7365;
}

.stat-card:nth-child(4)::before {
    background: #a58d7b;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.stat-icon {
    width: 65px;
    height: 65px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-right: 20px;
    color: var(--cream);
}

.stat-card:nth-child(1) .stat-icon {
    background: var(--royal-brown);
}

.stat-card:nth-child(2) .stat-icon {
    background: var(--accent);
}

.stat-card:nth-child(3) .stat-icon {
    background: #8a7365;
}

.stat-card:nth-child(4) .stat-icon {
    background: #a58d7b;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--royal-brown);
    line-height: 1;
    margin-bottom: 5px;
}

.stat-label {
    color: #8a7365;
    font-size: 0.95rem;
}

/* ========== DASHBOARD SECTIONS ========== */
.dashboard-section {
    background: var(--light);
    border-radius: 15px;
    padding: 30px;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    color: var(--royal-brown);
}

.section-title i {
    margin-right: 12px;
    color: var(--accent);
    font-size: 1.3rem;
}

.view-all {
    color: var(--royal-brown);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.view-all:hover {
    color: var(--accent);
}

.view-all i {
    margin-left: 5px;
    transition: all 0.3s ease;
}

.view-all:hover i {
    transform: translateX(3px);
}

/* ========== TABLES ========== */
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th, 
.data-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid rgba(109, 76, 61, 0.1);
}

.data-table th {
    background-color: rgba(109, 76, 61, 0.05);
    color: var(--royal-brown);
    font-weight: 600;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.data-table tr:hover {
    background-color: rgba(109, 76, 61, 0.03);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: #b38f00;
}

.status-approved {
    background: rgba(76, 175, 80, 0.2);
    color: #2e7d32;
}

.status-rejected {
    background: rgba(244, 67, 54, 0.2);
    color: #c62828;
}

/* ========== PET CARDS ========== */
.pets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.pet-card {
    background: var(--light);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.pet-image {
    height: 160px;
    position: relative;
    overflow: hidden;
}

.pet-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.pet-card:hover .pet-image img {
    transform: scale(1.05);
}

.pet-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--accent);
    color: var(--light);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.pet-info {
    padding: 20px;
}

.pet-name {
    font-size: 1.2rem;
    margin-bottom: 8px;
    color: var(--royal-brown);
    font-family: 'Playfair Display', serif;
}

.pet-details {
    color: #8a7365;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.pet-actions {
    display: flex;
    justify-content: space-between;
}

.btn {
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    margin-right: 5px;
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

/* ========== QUICK ACTIONS ========== */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.action-card {
    background: var(--light);
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    color: var(--cream);
}

.action-card:nth-child(1) .action-icon {
    background: var(--royal-brown);
}

.action-card:nth-child(2) .action-icon {
    background: var(--accent);
}

.action-card:nth-child(3) .action-icon {
    background: #8a7365;
}

.action-card:nth-child(4) .action-icon {
    background: #a58d7b;
}

.action-title {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: var(--royal-brown);
    font-family: 'Playfair Display', serif;
}

/* ========== RESPONSIVE DESIGN ========== */
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
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
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
    .stats-grid {
        grid-template-columns: 1fr;
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
    .quick-actions {
        grid-template-columns: 1fr;
    }
}

/* ========== MENU TOGGLE BUTTON ========== */
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

/* ========== EMPTY STATE ========== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #8a7365;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: rgba(109, 76, 61, 0.2);
}

.empty-state p {
    margin-bottom: 20px;
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
        <li><a href="shelter_request.php" class="active"><i class="fas fa-heart"></i> <span>Adoption Requests</span></a></li>
        <li><a href="#"><i class="fas fa-chart-line"></i> <span>Reports</span></a></li>
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
        <h1>Adoption Requests</h1>
        <p>Manage adoption requests for your shelter pets</p>
        <div class="date-display">
            <?php echo date('l, F j, Y'); ?>
        </div>
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

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-paw"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $pets_count; ?></div>
            <div class="stat-label">Adoptable Pets</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $products_count; ?></div>
            <div class="stat-label">Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $adoptions_count; ?></div>
            <div class="stat-label">Adoptions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo $pending_requests; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>
</div>

<!-- Adoption Requests Section -->
<div class="dashboard-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-heart"></i> Adoption Requests for Shelter Pets</h2>
    </div>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="pets-grid">
            <?php while($pet = mysqli_fetch_assoc($result)): ?>
                <div class="pet-card">
                    <div class="pet-image">
                        <img src="<?= htmlspecialchars($pet['photo']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>">
                        <div class="pet-badge">Shelter Pet</div>
                    </div>
                    <div class="pet-info">
                        <h3 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h3>
                        <div class="pet-details">
                            <p>Breed: <?= htmlspecialchars($pet['breed']) ?></p>
                            <p>Age: <?= htmlspecialchars($pet['age']) ?> yrs</p>
                            <p>Status: <?= htmlspecialchars($pet['status']) ?></p>
                        </div>
                        <div class="pet-actions">
                            <a href="pet_details.php?id=<?= $pet['id'] ?>" class="btn btn-primary">View Details</a>
                            <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn btn-secondary">Edit</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-paw"></i>
            <p>No adoption requests for your shelter pets.</p>
            <a href="add_pet.php" class="btn btn-primary">Add Your First Pet</a>
        </div>
    <?php endif; ?>
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

// Update date display
function updateDate() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.querySelector('.date-display').textContent = now.toLocaleDateString('en-US', options);
}

// Initial call
updateDate();
</script>

</body>
</html>