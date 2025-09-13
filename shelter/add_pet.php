<?php
session_start();
include '../config.php'; // mysqli connection from config.php

// Check if user is logged in and is a shelter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    die("❌ Access denied. Only shelters can add pets!");
}

$shelterId = $_SESSION['user_id'];

// Process form submission
$success_msg = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $size = $_POST['size'];
    $description = $_POST['description'];

    // MySQLi prepared statement
    $stmt = $conn->prepare("INSERT INTO pets (shelter_id, name, species, breed, age, gender, size, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        // Bind parameters: i = integer, s = string
        $stmt->bind_param("isssisss", $shelterId, $pet_name, $species, $breed, $age, $gender, $size, $description);

        if ($stmt->execute()) {
            $success_msg = "✅ Adoptable pet added successfully!";
        } else {
            $error_msg = "❌ Execute failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_msg = "❌ Prepare failed: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurShield - Add Adoptable Pet</title>
    
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

    /* ========== FORM STYLES ========== */
    .form-container {
        background: var(--light);
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        border: 1px solid rgba(109, 76, 61, 0.1);
    }

    .form-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(109, 76, 61, 0.1);
    }

    .form-title {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        color: var(--royal-brown);
    }

    .form-title i {
        margin-right: 12px;
        color: var(--accent);
        font-size: 1.3rem;
    }

    .form-description {
        color: #8a7365;
        margin-top: 10px;
        font-size: 0.95rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--royal-brown);
    }

    .form-input {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid rgba(109, 76, 61, 0.2);
        border-radius: 8px;
        background: var(--cream);
        color: var(--royal-brown);
        font-family: 'Montserrat', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
    }

    .form-select {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid rgba(109, 76, 61, 0.2);
        border-radius: 8px;
        background: var(--cream);
        color: var(--royal-brown);
        font-family: 'Montserrat', sans-serif;
        font-size: 0.95rem;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236d4c3d' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(109, 76, 61, 0.1);
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
        .form-grid {
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

    /* ========== ALERT STYLES ========== */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .alert i {
        margin-right: 12px;
        font-size: 1.2rem;
    }

    .alert-success {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        border-left: 4px solid #2e7d32;
    }

    .alert-error {
        background: rgba(244, 67, 54, 0.15);
        color: #c62828;
        border-left: 4px solid #c62828;
    }
    
    .required {
        color: #c62828;
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
        <li><a href="add_pet.php" class="active"><i class="fas fa-plus-circle"></i> <span>Add Adoptable Pet</span></a></li>
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
        <h1>Add Adoptable Pet</h1>
        <p>Complete the form below to add a new pet to your shelter.</p>
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

<!-- PHP Message Display -->
<?php if ($success_msg): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
    </div>
<?php endif; ?>

<!-- Form Container -->
<div class="form-container">
    <div class="form-header">
        <h2 class="form-title"><i class="fas fa-paw"></i> Pet Information</h2>
        <p class="form-description">Please provide detailed information about the pet you're adding to the adoption program.</p>
    </div>

    <form method="post" id="addPetForm">
        <div class="form-grid">
            <div class="form-group">
                <label for="pet_name" class="form-label">Pet Name <span class="required">*</span></label>
                <input type="text" id="pet_name" name="pet_name" class="form-input" placeholder="Enter pet's name" required value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="species" class="form-label">Species <span class="required">*</span></label>
                <select id="species" name="species" class="form-select" required>
                    <option value="">Select a species</option>
                    <option value="dog" <?php echo (isset($_POST['species']) && $_POST['species'] == 'dog') ? 'selected' : ''; ?>>Dog</option>
                    <option value="cat" <?php echo (isset($_POST['species']) && $_POST['species'] == 'cat') ? 'selected' : ''; ?>>Cat</option>
                    <option value="bird" <?php echo (isset($_POST['species']) && $_POST['species'] == 'bird') ? 'selected' : ''; ?>>Bird</option>
                    <option value="rabbit" <?php echo (isset($_POST['species']) && $_POST['species'] == 'rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                    <option value="other" <?php echo (isset($_POST['species']) && $_POST['species'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="breed" class="form-label">Breed</label>
                <input type="text" id="breed" name="breed" class="form-input" placeholder="Enter breed" value="<?php echo isset($_POST['breed']) ? htmlspecialchars($_POST['breed']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="age" class="form-label">Age (years)</label>
                <input type="number" id="age" name="age" class="form-input" placeholder="Enter age" min="0" max="30" step="0.1" value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-select">
                    <option value="">Select gender</option>
                    <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="size" class="form-label">Size</label>
                <select id="size" name="size" class="form-select">
                    <option value="">Select size</option>
                    <option value="small" <?php echo (isset($_POST['size']) && $_POST['size'] == 'small') ? 'selected' : ''; ?>>Small</option>
                    <option value="medium" <?php echo (isset($_POST['size']) && $_POST['size'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="large" <?php echo (isset($_POST['size']) && $_POST['size'] == 'large') ? 'selected' : ''; ?>>Large</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-input form-textarea" placeholder="Tell us about this pet's personality, history, and any special needs..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>

        <div class="form-actions">
            <a href="my_pets.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Pet
            </button>
        </div>
    </form>
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

// Form validation
document.getElementById('addPetForm').addEventListener('submit', function(e) {
    const petName = document.getElementById('pet_name').value.trim();
    const species = document.getElementById('species').value;
    
    if (!petName) {
        e.preventDefault();
        alert('Please enter a pet name');
        document.getElementById('pet_name').focus();
        return;
    }
    
    if (!species) {
        e.preventDefault();
        alert('Please select a species');
        document.getElementById('species').focus();
        return;
    }
});
</script>

</body>
</html>