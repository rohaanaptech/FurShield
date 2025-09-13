<?php
include '../config.php';
include '../auth.php';
requireOwner();

if ($_SESSION['role'] != 'owner') {
    die("❌ Access denied!");
}

$ownerId = $_SESSION['user_id'];

// Fetch data
$result = $conn->query("SELECT name, email FROM users WHERE id=$ownerId");
$user = $result->fetch_assoc();

$success_message = "";
$error_message = "";

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $hashed, $ownerId);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $ownerId);
    }

    if ($stmt->execute()) {
        $success_message = "✅ Profile updated successfully!";
        // Update session name if changed
        $_SESSION['name'] = $name;
        // Refresh user data
        $result = $conn->query("SELECT name, email FROM users WHERE id=$ownerId");
        $user = $result->fetch_assoc();
    } else {
        $error_message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - FurShield</title>
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

        .dashboard-header {
            background: var(--light);
            padding: 15px 0;
            box-shadow: 0 2px 15px rgba(109, 76, 61, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--royal-brown);
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            color: var(--accent);
        }

        .user-welcome {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            font-weight: 600;
            font-size: 1.2rem;
        }

        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(to bottom, var(--royal-brown), #5a3e30);
            color: var(--light);
            padding: 30px 0;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 0 25px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-title i {
            color: var(--accent);
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: var(--light);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            opacity: 0.8;
        }

        .nav-link:hover, .nav-link.active {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link:hover::before, .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--accent);
        }

        .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.2rem;
        }

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
            margin-bottom: 0;
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

        .profile-container {
            background: var(--light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 700px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            font-size: 2.5rem;
            margin: 0 auto 20px;
            font-weight: 600;
        }

        .profile-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--royal-brown);
        }

        .profile-subtitle {
            color: var(--medium-brown);
            margin-bottom: 0;
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

        .password-note {
            color: var(--medium-brown);
            font-size: 0.9rem;
            margin-top: 5px;
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

        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .nav-items {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .nav-item {
                margin-bottom: 0;
            }
            
            .nav-link {
                padding: 10px 15px;
                border-radius: 8px;
            }
            
            .nav-link::before {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .profile-container {
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
            
            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .profile-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a class="navbar-brand" href="../index.php">
                    <i class="fas fa-paw"></i> FurShield
                </a>
                <div class="user-welcome">
                    <div class="user-info d-none d-md-block">
                        <span>Welcome, <?php echo $_SESSION['name']; ?>!</span>
                    </div>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                    </div>
                    <a href="../logout.php" class="btn btn-sm btn-outline">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title"><i class="fas fa-paw"></i> Owner Dashboard</h3>
            </div>
            <nav class="nav-items">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="add_pet.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Add Pet
                    </a>
                </div>
                <div class="nav-item">
                    <a href="my_pets.php" class="nav-link">
                        <i class="fas fa-paw"></i> My Pets
                    </a>
                </div>
                <div class="nav-item">
                    <a href="book_appointment.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i> Book Appointment
                    </a>
                </div>
                <div class="nav-item">
                    <a href="my_appointments.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i> My Appointments
                    </a>
                </div>
                <div class="nav-item">
                    <a href="view_health.php" class="nav-link">
                        <i class="fas fa-heartbeat"></i> Health Records
                    </a>
                </div>
                <div class="nav-item">
                    <a href="profile.php" class="nav-link active">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">My Profile</h1>
            </div>

            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <h2 class="profile-title"><?php echo $user['name']; ?></h2>
                    <p class="profile-subtitle">Pet Owner Account</p>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="message success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label class="form-label" for="name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password"><i class="fas fa-lock"></i> New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password (leave blank to keep current)">
                        <p class="password-note">Leave this field blank if you don't want to change your password.</p>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>