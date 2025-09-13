<?php
include '../config.php';
include '../auth.php';

// Check if user is an owner
if ($role !== 'owner') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - FurShield</title>
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

        /* Header */
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

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* Sidebar */
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: var(--cream);
        }

        .dashboard-hero {
            background: linear-gradient(to right, rgba(200, 155, 123, 0.1), rgba(109, 76, 61, 0.05));
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .welcome-text h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .welcome-text p {
            color: var(--medium-brown);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .hero-icon {
            font-size: 4rem;
            color: var(--accent);
            opacity: 0.7;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(109, 76, 61, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--royal-brown));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            font-size: 1.5rem;
            margin-right: 20px;
        }

        .stat-content h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            color: var(--royal-brown);
        }

        .stat-content p {
            color: var(--medium-brown);
            margin-bottom: 0;
        }

        /* Quick Actions */
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            background: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-left: 4px solid var(--accent);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(109, 76, 61, 0.1);
        }

        .action-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: rgba(200, 155, 123, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .action-title {
            font-size: 1.3rem;
            margin-bottom: 0;
        }

        .action-description {
            color: var(--medium-brown);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .action-btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.2);
            color: var(--light);
        }

        /* Recent Pets */
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .pet-card {
            background: var(--light);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(109, 76, 61, 0.1);
        }

        .pet-image {
            height: 180px;
            background: linear-gradient(135deg, var(--light-accent), var(--cream));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 3rem;
        }

        .pet-details {
            padding: 20px;
        }

        .pet-name {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--royal-brown);
        }

        .pet-breed {
            color: var(--medium-brown);
            margin-bottom: 15px;
            display: block;
        }

        .pet-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .pet-info span {
            display: flex;
            align-items: center;
            color: var(--medium-brown);
            font-size: 0.9rem;
        }

        .pet-info i {
            margin-right: 5px;
            color: var(--accent);
        }

        .pet-actions {
            display: flex;
            gap: 10px;
        }

        .pet-btn {
            flex: 1;
            padding: 8px 15px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .pet-btn-primary {
            background: var(--royal-brown);
            color: var(--light);
        }

        .pet-btn-primary:hover {
            background: var(--accent);
            color: var(--light);
        }

        .pet-btn-outline {
            border: 1px solid var(--royal-brown);
            color: var(--royal-brown);
        }

        .pet-btn-outline:hover {
            background: var(--royal-brown);
            color: var(--light);
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
            
            .hero-icon {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .stats-grid, .actions-grid, .pets-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-text h1 {
                font-size: 2rem;
            }
            
            .user-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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

    <!-- Dashboard Layout -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title"><i class="fas fa-paw"></i> Owner Dashboard</h3>
            </div>
            <nav class="nav-items">
                <div class="nav-item">
                    <a href="#" class="nav-link active">
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
                    <a href="profile.php" class="nav-link">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Hero -->
            <div class="dashboard-hero">
                <div class="welcome-text">
                    <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
                    <p>Manage your pets, appointments, and health records all in one place.</p>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-dog"></i>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stat-content">
                        <h3>3</h3>
                        <p>Registered Pets</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <h3>2</h3>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>5</h3>
                        <p>Health Records</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>3</h3>
                        <p>Items in Cart</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-header">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3 class="action-title">Add New Pet</h3>
                    </div>
                    <p class="action-description">Register a new pet to your profile and start managing their health records.</p>
                    <a href="add_pet.php" class="action-btn">Add Pet</a>
                </div>
                <div class="action-card">
                    <div class="action-header">
                        <div class="action-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="action-title">Book Appointment</h3>
                    </div>
                    <p class="action-description">Schedule a vet appointment for your pet's health checkup or consultation.</p>
                    <a href="book_appointment.php" class="action-btn">Book Now</a>
                </div>
                <div class="action-card">
                    <div class="action-header">
                        <div class="action-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="action-title">Shop Products</h3>
                    </div>
                    <p class="action-description">Browse our collection of pet food, toys, and accessories for your furry friends.</p>
                    <a href="products.php" class="action-btn">Shop Now</a>
                </div>
            </div>

            <!-- Recent Pets -->
            <h2 class="section-title">Your Pets</h2>
            <div class="pets-grid">
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fas fa-dog"></i>
                    </div>
                    <div class="pet-details">
                        <h3 class="pet-name">Max</h3>
                        <span class="pet-breed">Golden Retriever</span>
                        <div class="pet-info">
                            <span><i class="fas fa-birthday-cake"></i> 3 years</span>
                            <span><i class="fas fa-venus"></i> Male</span>
                        </div>
                        <div class="pet-actions">
                            <a href="#" class="pet-btn pet-btn-primary">View Health</a>
                            <a href="#" class="pet-btn pet-btn-outline">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fas fa-cat"></i>
                    </div>
                    <div class="pet-details">
                        <h3 class="pet-name">Luna</h3>
                        <span class="pet-breed">Siamese Cat</span>
                        <div class="pet-info">
                            <span><i class="fas fa-birthday-cake"></i> 2 years</span>
                            <span><i class="fas fa-mars"></i> Female</span>
                        </div>
                        <div class="pet-actions">
                            <a href="#" class="pet-btn pet-btn-primary">View Health</a>
                            <a href="#" class="pet-btn pet-btn-outline">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="pet-details">
                        <h3 class="pet-name">Add New Pet</h3>
                        <span class="pet-breed">Register a new pet</span>
                        <div class="pet-info">
                            <span>Click below to add</span>
                        </div>
                        <div class="pet-actions">
                            <a href="add_pet.php" class="pet-btn pet-btn-primary" style="flex: auto;">Add Pet</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>