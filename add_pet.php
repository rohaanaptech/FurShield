<?php
session_start();
include 'config.php';
include 'auth.php';
requireOwner(); 

$ownerId = $user_id;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$ownerId = $_SESSION['user_id'];

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $notes = $_POST['notes'];
    
    // Handle file upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/pets/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        // Check if file is an image
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $photo = 'uploads/pets/' . $fileName;
            } else {
                $error_message = "❌ Error uploading file.";
            }
        } else {
            $error_message = "❌ Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error_message)) {
   $stmt = $conn->prepare("
    INSERT INTO adoptable_pets (owner_id, pet_name, species, breed, age, gender, size, description, photo) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isssdssss", $ownerId, $name, $species, $breed, $age, $gender, $size, $description, $photo);


        if ($stmt->execute()) {
            $success_message = "✅ Pet added successfully!";
        } else {
            $error_message = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet - FurShield</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
            --light-accent: #e7d8cc;
            --medium-brown: #8a7365;
            --shadow: 0 10px 30px rgba(109, 76, 61, 0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: var(--cream);
            color: var(--royal-brown);
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--royal-brown);
        }

       
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: var(--cream);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-accent);
        }

        .page-title {
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -17px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--royal-brown);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--accent);
            transform: translateX(-5px);
        }

        .back-link i {
            margin-right: 8px;
        }

        /* Form Container */
        .form-container {
            background: var(--light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
            color: var(--royal-brown);
            position: relative;
            padding-bottom: 15px;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--royal-brown);
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 10px;
            color: var(--accent);
            width: 20px;
            text-align: center;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid var(--light-accent);
            border-radius: 12px;
            font-size: 1rem;
            color: var(--dark);
            transition: var(--transition);
            background-color: var(--cream);
            font-family: 'Montserrat', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(200, 155, 123, 0.2);
            background-color: #fff;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236d4c3d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 16px;
        }

        .file-upload {
            position: relative;
            display: block;
        }

        .file-upload-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px dashed var(--light-accent);
            border-radius: 12px;
            background-color: var(--cream);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-upload-input:hover {
            border-color: var(--accent);
            background-color: rgba(200, 155, 123, 0.1);
        }

        .file-upload-preview {
            margin-top: 15px;
            text-align: center;
            display: none;
        }

        .file-upload-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            border: none;
            border-radius: 12px;
            padding: 16px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.3);
        }

        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background-color: #f0fff4;
            color: #2d8045;
            border: 2px solid #c6f6d5;
        }

        .error {
            background-color: #fff5f5;
            color: #c53030;
            border: 2px solid #fed7d7;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .sidebar-header {
                padding: 0 0 15px 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .nav-items {
                display: none;
                flex-direction: column;
            }
            
            .nav-items.active {
                display: flex;
            }
            
            .nav-item {
                margin-bottom: 0;
            }
            
            .nav-link {
                padding: 12px 15px;
                border-radius: 8px;
            }
            
            .nav-link::before {
                display: none;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 25px 20px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                display: none;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
  <?php include 'header.php'?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Add New Pet</h1>
                <a href="my_pets.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to My Pets
                </a>
            </div>

            <div class="form-container">
                <h2 class="form-title">Enter Your Pet's Details</h2>
                
                <?php if (!empty($success_message)): ?>
                    <div class="message success">
                        <?php echo $success_message; ?> 
                        <a href="my_pets.php" style="color: #2d8045; text-decoration: underline; margin-left: 10px;">View Pets</a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="name"><i class="fas fa-signature"></i> Pet Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your pet's name" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="species"><i class="fas fa-paw"></i> Species</label>
                            <input type="text" class="form-control" id="species" name="species" placeholder="e.g., Dog, Cat, Bird" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="breed"><i class="fas fa-dna"></i> Breed</label>
                            <input type="text" class="form-control" id="breed" name="breed" placeholder="e.g., Golden Retriever, Siamese">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="age"><i class="fas fa-birthday-cake"></i> Age</label>
                            <input type="number" class="form-control" id="age" name="age" placeholder="Age in years" min="0" max="30">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Unknown">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="photo"><i class="fas fa-camera"></i> Pet Photo</label>
                        <div class="file-upload">
                            <label for="photo" class="file-upload-input">
                                <i class="fas fa-cloud-upload-alt"></i> Click to upload or drag and drop
                                <br>
                                <small>JPG, PNG, GIF files only (Max 5MB)</small>
                                <input type="file" id="photo" name="photo" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            </label>
                            <div class="file-upload-preview" id="imagePreview">
                                <img src="" alt="Image preview" id="preview">
                                <button type="button" onclick="removeImage()" class="btn btn-sm btn-outline mt-2">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="notes"><i class="fas fa-sticky-note"></i> Notes</label>
                        <textarea class="form-control" id="notes" name="notes" placeholder="Any special notes about your pet (diet, behavior, medical conditions, etc.)"></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus-circle"></i> Add Pet
                    </button>
                </form>
            </div>
        </main>
    </div>


    <!-- footer -->

    <?php include 'footer.php'?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('navItems').classList.toggle('active');
        });

        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('photo');
            const previewContainer = document.getElementById('imagePreview');
            
            input.value = '';
            previewContainer.style.display = 'none';
        }

        // Drag and drop functionality
        const fileUploadInput = document.querySelector('.file-upload-input');
        
        fileUploadInput.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--accent)';
            this.style.backgroundColor = 'rgba(200, 155, 123, 0.2)';
        });
        
        fileUploadInput.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--light-accent)';
            this.style.backgroundColor = 'var(--cream)';
        });
        
        fileUploadInput.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = 'var(--light-accent)';
            this.style.backgroundColor = 'var(--cream)';
            
            const files = e.dataTransfer.files;
            if (files.length) {
                document.getElementById('photo').files = files;
                previewImage(document.getElementById('photo'));
            }
        });
    </script>
</body>
</html>