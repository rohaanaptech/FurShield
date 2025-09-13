<?php
session_start();
include '../config.php';

// Check if user is logged in and is a shelter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../login.php");
    exit();
}

$shelter_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $stock = trim($_POST['stock']);
    
    // Validate inputs
    if (empty($name) || empty($category) || empty($price) || empty($stock)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } elseif (!is_numeric($price) || $price <= 0) {
        $message = "Price must be a valid number greater than 0.";
        $message_type = "error";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $message = "Stock quantity must be a valid non-negative number.";
        $message_type = "error";
    } else {
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/products/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            // Check if image file is actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $image_path = $file_path;
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $message_type = "error";
                }
            } else {
                $message = "File is not an image.";
                $message_type = "error";
            }
        }
        
        // If no errors, insert into database
        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, stock_quantity, image_path, shelter_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsisi", $name, $category, $price, $description, $stock, $image_path, $shelter_id);
            
            if ($stmt->execute()) {
                $message = "Product added successfully!";
                $message_type = "success";
                
                // Clear form fields
                $name = $category = $price = $description = $stock = '';
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = "error";
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
<title>FurShield - Add Product</title>

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
    --success: #4CAF50;
    --error: #F44336;
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

/* ========== FORM CONTAINER ========== */
.form-container {
    background: var(--light);
    border-radius: 15px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 30px;
    border: 1px solid rgba(109, 76, 61, 0.1);
}

.form-header {
    background: linear-gradient(135deg, var(--royal-brown), #8B5A2B);
    color: white;
    padding: 25px 30px;
    position: relative;
}

.form-header h2 {
    color: white;
    margin-bottom: 5px;
    font-size: 1.8rem;
}

.form-header p {
    opacity: 0.9;
    font-size: 0.95rem;
}

.form-header-icon {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 2.5rem;
    opacity: 0.2;
}

.form-body {
    padding: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--royal-brown);
}

input, textarea, select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.form-footer {
    padding: 20px 30px;
    background: #f9f9f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(109, 76, 61, 0.1);
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background: var(--royal-brown);
    color: white;
}

.btn-primary:hover {
    background: #5a3e30;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #e9ecef;
    color: #495057;
}

.btn-secondary:hover {
    background: #dee2e6;
}

.message {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.message i {
    margin-right: 10px;
}

.success {
    background-color: rgba(76, 175, 80, 0.15);
    color: var(--success);
    border-left: 4px solid var(--success);
}

.error {
    background-color: rgba(244, 67, 54, 0.15);
    color: var(--error);
    border-left: 4px solid var(--error);
}

/* Image upload preview */
.image-upload {
    position: relative;
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-upload:hover {
    border-color: var(--accent);
}

.image-upload input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-icon {
    font-size: 2.5rem;
    color: #ccc;
    margin-bottom: 10px;
}

.image-upload-text {
    color: #777;
}

.image-preview {
    margin-top: 15px;
    display: none;
}

.image-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
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
    .form-footer {
        flex-direction: column;
        gap: 15px;
    }
    .btn {
        width: 100%;
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

@media (max-width: 768px) {
    .menu-toggle {
        display: flex;
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
    <!-- <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button> -->

    <!-- Top header -->
    <div class="top-header">
        <div class="welcome-message">
            <h1>Add New Product</h1>
            <p>Fill in the details below to add a new product to your store</p>
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

    <div class="form-container">
        <div class="form-header">
            <h2>Add New Product</h2>
            <p>Fill in the details below to add a new product to your store</p>
            <i class="fas fa-box form-header-icon"></i>
        </div>

        <div class="form-body">
            <?php if(!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" placeholder="Enter product name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <option value="food" <?php echo (isset($category) && $category == 'food') ? 'selected' : ''; ?>>Pet Food</option>
                            <option value="toy" <?php echo (isset($category) && $category == 'toy') ? 'selected' : ''; ?>>Toys</option>
                            <option value="accessory" <?php echo (isset($category) && $category == 'accessory') ? 'selected' : ''; ?>>Accessories</option>
                            <option value="grooming" <?php echo (isset($category) && $category == 'grooming') ? 'selected' : ''; ?>>Grooming</option>
                            <option value="health" <?php echo (isset($category) && $category == 'health') ? 'selected' : ''; ?>>Health Supplies</option>
                            <option value="bedding" <?php echo (isset($category) && $category == 'bedding') ? 'selected' : ''; ?>>Bedding</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" min="0" placeholder="Enter quantity" value="<?php echo isset($stock) ? htmlspecialchars($stock) : ''; ?>" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe the product in detail"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Product Image</label>
                        <div class="image-upload">
                            <input type="file" id="image" name="image" accept="image/*">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <p class="image-upload-text">Click to upload or drag and drop</p>
                            <p class="image-upload-hint">PNG, JPG, JPEG up to 5MB</p>
                            
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Image Preview">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

// Image preview functionality
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');

imageInput.addEventListener('change', function() {
    const file = this.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.addEventListener('load', function() {
            imagePreview.style.display = 'block';
            imagePreview.querySelector('img').setAttribute('src', this.result);
        });
        
        reader.readAsDataURL(file);
    } else {
        imagePreview.style.display = 'none';
        imagePreview.querySelector('img').setAttribute('src', '');
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    let valid = true;
    
    // Check required fields
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            valid = false;
            field.style.borderColor = 'var(--error)';
        } else {
            field.style.borderColor = '#ddd';
        }
    });
    
    // Price validation
    const priceField = document.getElementById('price');
    if (priceField.value && parseFloat(priceField.value) <= 0) {
        valid = false;
        priceField.style.borderColor = 'var(--error)';
    }
    
    // Stock validation
    const stockField = document.getElementById('stock');
    if (stockField.value && parseInt(stockField.value) < 0) {
        valid = false;
        stockField.style.borderColor = 'var(--error)';
    }
    
    if (!valid) {
        e.preventDefault();
        alert('Please fill all required fields with valid values.');
    }
});
</script>

</body>
</html>