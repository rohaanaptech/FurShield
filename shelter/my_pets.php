<?php
session_start();
include '../config.php';

if ($_SESSION['role'] != 'shelter') {
    die("❌ Only shelters can view pets!");
}

$shelterId = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM pets WHERE shelter_id=$shelterId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurShield - My Adoptable Pets</title>
    
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

    /* ========== TABLE STYLES ========== */
    .table-container {
        background: var(--light);
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        border: 1px solid rgba(109, 76, 61, 0.1);
        overflow-x: auto;
    }

    .table-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(109, 76, 61, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        color: var(--royal-brown);
    }

    .table-title i {
        margin-right: 12px;
        color: var(--accent);
        font-size: 1.3rem;
    }

    .table-description {
        color: #8a7365;
        margin-top: 10px;
        font-size: 0.95rem;
        flex-basis: 100%;
    }

    .pets-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .pets-table th {
        background-color: rgba(109, 76, 61, 0.1);
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: var(--royal-brown);
        border-bottom: 2px solid rgba(109, 76, 61, 0.2);
    }

    .pets-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(109, 76, 61, 0.1);
        vertical-align: middle;
    }

    .pets-table tr:last-child td {
        border-bottom: none;
    }

    .pets-table tr:hover {
        background-color: rgba(200, 155, 123, 0.05);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-available {
        background-color: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }

    .status-pending {
        background-color: rgba(255, 152, 0, 0.15);
        color: #ef6c00;
    }

    .status-adopted {
        background-color: rgba(244, 67, 54, 0.15);
        color: #c62828;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(109, 76, 61, 0.1);
        color: var(--royal-brown);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-icon:hover {
        background: var(--royal-brown);
        color: var(--cream);
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #8a7365;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: rgba(109, 76, 61, 0.3);
    }

    .empty-state p {
        margin-bottom: 20px;
    }

    /* ========== BUTTON STYLES ========== */
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
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
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(109, 76, 61, 0.2);
    }

    .btn-secondary {
        background: rgba(109, 76, 61, 0.1);
        color: var(--royal-brown);
    }

    .btn-secondary:hover {
        background: rgba(109, 76, 61, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
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
        .pets-table {
            font-size: 0.9rem;
        }
        .pets-table th,
        .pets-table td {
            padding: 10px;
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
        <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="add_pet.php"><i class="fas fa-plus-circle"></i> <span>Add Adoptable Pet</span></a></li>
        <li><a href="my_pets.php"><i class="fas fa-paw"></i> <span>My Adoptable Pets</span></a></li>
        <li><a href="add_product.php"><i class="fas fa-cart-plus"></i> <span>Add Product</span></a></li>
        <li><a href="my_products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
        <li><a href="shelter_request.php"><i class="fas fa-heart"></i> <span>Adoption Requests</span></a></li>
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
        <h1>My Adoptable Pets</h1>
        <p>Manage all pets currently available for adoption at your shelter.</p>
    </div>

    <!-- User profile -->
    <div class="user-profile">
        <button class="user-btn" id="userDropdownBtn">
            <div class="user-avatar">S</div>
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

<!-- Table Container -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title"><i class="fas fa-paw"></i> Pets List</h2>
        <a href="add_pet.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Pet
        </a>
        <p class="table-description">All pets currently in your shelter's adoption program are listed below.</p>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="pets-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Species</th>
                    <th>Breed</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    // Determine status class
                    $status_class = '';
                    if ($row['status'] == 'available') {
                        $status_class = 'status-available';
                    } elseif ($row['status'] == 'pending') {
                        $status_class = 'status-pending';
                    } elseif ($row['status'] == 'adopted') {
                        $status_class = 'status-adopted';
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['species'])) ?></td>
                        <td><?= !empty($row['breed']) ? htmlspecialchars($row['breed']) : '—' ?></td>
                        <td><?= !empty($row['age']) ? htmlspecialchars($row['age']) . ' yrs' : '—' ?></td>
                        <td><?= !empty($row['gender']) ? htmlspecialchars(ucfirst($row['gender'])) : '—' ?></td>
                        <td><?= !empty($row['size']) ? htmlspecialchars(ucfirst($row['size'])) : '—' ?></td>
                        <td><span class="status-badge <?= $status_class ?>"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit_pet.php?id=<?= $row['id'] ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="view_pet.php?id=<?= $row['id'] ?>" class="btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-paw"></i>
            <h3>No Pets Added Yet</h3>
            <p>You haven't added any pets to your adoption program yet.</p>
            <a href="add_pet.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Your First Pet
            </a>
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
</script>

</body>
</html>