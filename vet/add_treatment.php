<?php
session_start();
include '../config.php';

// Sirf vets ke liye
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$vetId = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Agar form submit hua
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_id = $_POST['pet_id'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $visit_date = date('Y-m-d'); // aaj ki date

    $stmt = $conn->prepare("INSERT INTO health_records (pet_id, vet_id, visit_date, diagnosis, treatment) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisss", $pet_id, $vetId, $visit_date, $diagnosis, $treatment);

    if ($stmt->execute()) {
        $success_message = "✅ Treatment record added successfully!";
    } else {
        $error_message = "❌ Error: " . $stmt->error;
    }
}

// Vet ke appointments ke pets list nikaalo
$pets = $conn->query("
    SELECT DISTINCT p.id, p.name, p.species
    FROM appointments a
    JOIN pets p ON a.pet_id = p.id
    WHERE a.vet_id = $vetId AND a.status = 'Approved'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurShield - Add Treatment Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
        }
        
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
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--royal-brown);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(to bottom, rgba(248, 244, 233, 0.95), rgba(248, 244, 233, 0.9));
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(109, 76, 61, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--royal-brown);
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cream);
            font-weight: bold;
        }
        
        /* Dashboard Layout */
        .dashboard {
            display: flex;
            margin-top: 100px;
            min-height: calc(100vh - 180px);
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--royal-brown);
            color: var(--cream);
            padding: 30px 0;
            border-radius: 15px;
            margin-right: 20px;
            height: fit-content;
            position: sticky;
            top: 120px;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: var(--cream);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu a.active {
            background-color: var(--accent);
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 20px 0;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .form-container {
            background-color: var(--light);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--royal-brown);
        }
        
        .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(109, 76, 61, 0.2);
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            background-color: var(--cream);
            color: var(--royal-brown);
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(200, 155, 123, 0.2);
        }
        
        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .pet-option {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }
        
        .pet-icon {
            margin-right: 10px;
            color: var(--accent);
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--royal-brown);
            color: var(--cream);
        }
        
        .btn-primary:hover {
            background-color: var(--accent);
        }
        
        .btn-outline {
            border: 2px solid var(--royal-brown);
            color: var(--royal-brown);
            background-color: transparent;
        }
        
        .btn-outline:hover {
            background-color: var(--royal-brown);
            color: var(--cream);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .alert-error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--royal-brown);
            color: var(--cream);
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }
        
        .copyright {
            opacity: 0.8;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: static;
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .sidebar-menu li {
                margin: 5px;
            }
            
            .sidebar-menu a {
                padding: 10px 15px;
                border-radius: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--royal-brown);
            opacity: 0.7;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent);
        }
        
        .empty-state a {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="dashboard.php" class="logo">
                    <i class="fas fa-paw"></i>
                    FurShield
                </a>
                
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['name'] ?? 'V', 0, 1)); ?></div>
                    <span>Dr. <?php echo $_SESSION['name'] ?? 'Vet'; ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <div class="container">
        <div class="dashboard">
            <!-- Sidebar Menu -->
            <div class="sidebar">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="my_records.php"><i class="fas fa-heartbeat"></i> Health Records</a></li>
                    <li><a href="add_treatment.php" class="active"><i class="fas fa-plus-circle"></i> Add Treatment</a></li>
                    <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h2 class="page-title"><i class="fas fa-plus-circle"></i> Add Treatment Record</h2>
                </div>
                
                <!-- Messages -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                
                <div class="form-container">
                    <?php if ($pets->num_rows > 0): ?>
                    <form method="post">
                        <div class="form-group">
                            <label class="form-label">Select Pet:</label>
                            <select name="pet_id" class="form-select" required>
                                <?php while($p = $pets->fetch_assoc()): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <div class="pet-option">
                                            <i class="fas fa-<?php echo $p['type'] == 'Dog' ? 'dog' : 'cat'; ?> pet-icon"></i>
                                            <?= $p['name'] ?>
                                        </div>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Diagnosis:</label>
                            <textarea name="diagnosis" class="form-textarea" required placeholder="Enter diagnosis details..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Treatment:</label>
                            <textarea name="treatment" class="form-textarea" required placeholder="Enter treatment details..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Record
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-paw"></i>
                        <h3>No Pets Available</h3>
                        <p>You need to have approved appointments before you can add treatment records.</p>
                        <a href="appointments.php" class="btn btn-outline">View Appointments</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="copyright">
                <p>&copy; 2023 FurShield. All rights reserved. | Logged in as: <?php echo $_SESSION['role']; ?></p>
            </div>
        </div>
    </footer>

    <script>
        // Simple animations
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-select, .form-textarea, .btn');
            
            // Animate form elements
            formElements.forEach((element, index) => {
                element.style.opacity = 0;
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    element.style.opacity = 1;
                    element.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>