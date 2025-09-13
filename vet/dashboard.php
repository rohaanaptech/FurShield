<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Sirf vets ko access allow karo
if ($_SESSION['role'] != 'vet') {
    die("❌ Access denied! Only vets can access this page.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurShield - Vet Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
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
        
        .welcome-banner {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .welcome-banner h1 {
            color: var(--cream);
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--light);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            text-align: center;
        }
        
        .stat-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--royal-brown);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--royal-brown);
            opacity: 0.8;
        }
        
        .appointment-section {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .view-all {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        
        .appointment-list {
            list-style: none;
        }
        
        .appointment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(109, 76, 61, 0.1);
        }
        
        .appointment-item:last-child {
            border-bottom: none;
        }
        
        .pet-info {
            display: flex;
            align-items: center;
        }
        
        .pet-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--royal-brown);
            font-weight: bold;
        }
        
        .pet-name {
            font-weight: 500;
        }
        
        .appointment-time {
            color: var(--accent);
            font-weight: 500;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
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
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .appointment-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .appointment-actions {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <i class="fas fa-paw"></i>
                    FurShield
                </a>
                
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['name'] ?? 'V', 0, 1)); ?></div>
                    <span>Welcome, <?php echo $_SESSION['name'] ?? 'Vet'; ?>!</span>
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
                    <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                                        <li><a href="add_treatment.php"><i class="fas fa-calendar-check"></i> Add Treatment</a></li>

                    <li><a href="my_records.php"><i class="fas fa-heartbeat"></i> Health Records</a></li>
                    <li><a href="../vet/profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h1>Welcome, Dr. <?php echo $_SESSION['name'] ?? 'Veterinarian'; ?>!</h1>
                    <p>Here's today's overview of your appointments and tasks.</p>
                </div>
                
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Today's Appointments</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number">3</div>
                        <div class="stat-label">Pending Follow-ups</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="stat-number">28</div>
                        <div class="stat-label">This Week's Patients</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">Tasks to Complete</div>
                    </div>
                </div>
                
                <!-- Upcoming Appointments -->
                <div class="appointment-section">
                    <div class="section-header">
                        <h3 class="section-title">Upcoming Appointments</h3>
                        <a href="appointments.php" class="view-all">View All</a>
                    </div>
                    
                    <ul class="appointment-list">
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div class="pet-avatar">
                                    <i class="fas fa-dog"></i>
                                </div>
                                <div>
                                    <div class="pet-name">Max (Golden Retriever)</div>
                                    <div>Vaccination</div>
                                </div>
                            </div>
                            <div class="appointment-time">10:00 AM</div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-primary">Details</a>
                            </div>
                        </li>
                        
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div class="pet-avatar">
                                    <i class="fas fa-cat"></i>
                                </div>
                                <div>
                                    <div class="pet-name">Whiskers (Siamese)</div>
                                    <div>Dental Checkup</div>
                                </div>
                            </div>
                            <div class="appointment-time">11:30 AM</div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-primary">Details</a>
                            </div>
                        </li>
                        
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div class="pet-avatar">
                                    <i class="fas fa-dog"></i>
                                </div>
                                <div>
                                    <div class="pet-name">Buddy (Labrador)</div>
                                    <div>Post-surgery Check</div>
                                </div>
                            </div>
                            <div class="appointment-time">2:15 PM</div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-primary">Details</a>
                            </div>
                        </li>
                        
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div class="pet-avatar">
                                    <i class="fas fa-cat"></i>
                                </div>
                                <div>
                                    <div class="pet-name">Luna (Maine Coon)</div>
                                    <div>Annual Checkup</div>
                                </div>
                            </div>
                            <div class="appointment-time">4:00 PM</div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-primary">Details</a>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <!-- Recent Activity -->
                <div class="appointment-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Activity</h3>
                        <a href="my_records.php" class="view-all">View All</a>
                    </div>
                    
                    <ul class="appointment-list">
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div>
                                    <div class="pet-name">Completed: Charlie's vaccination</div>
                                    <div>Yesterday at 3:45 PM</div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-outline">View Record</a>
                            </div>
                        </li>
                        
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div>
                                    <div class="pet-name">Added notes to Bella's dental record</div>
                                    <div>Yesterday at 11:20 AM</div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-outline">View Record</a>
                            </div>
                        </li>
                        
                        <li class="appointment-item">
                            <div class="pet-info">
                                <div>
                                    <div class="pet-name">Prescribed medication for Rocky</div>
                                    <div>2 days ago</div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <a href="#" class="btn btn-outline">View Record</a>
                            </div>
                        </li>
                    </ul>
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
            const statCards = document.querySelectorAll('.stat-card');
            const appointmentItems = document.querySelectorAll('.appointment-item');
            
            // Animate stat cards
            statCards.forEach((card, index) => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = 1;
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
            
            // Animate appointment items
            appointmentItems.forEach((item, index) => {
                item.style.opacity = 0;
                item.style.transform = 'translateX(-20px)';
                item.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    item.style.opacity = 1;
                    item.style.transform = 'translateX(0)';
                }, 300 + (index * 100));
            });
        });
    </script>
</body>
</html>