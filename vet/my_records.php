<?php
session_start();
include '../config.php';

// Sirf vets ke liye
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$vetId = $_SESSION['user_id'];
$records = $conn->query("
  SELECT h.id, h.visit_date, h.diagnosis, h.treatment, 
         p.name AS pet_name, u.name AS owner_name
  FROM health_records h
  JOIN pets p ON h.pet_id = p.id
  JOIN users u ON p.owner_id = u.id
  WHERE h.vet_id = $vetId
  ORDER BY h.visit_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Health Records - FurShield</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
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
        
        /* Records Section */
        .records-section {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            margin-bottom: 30px;
            overflow-x: auto;
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
        
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .records-table th {
            background-color: var(--cream);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--accent);
        }
        
        .records-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(109, 76, 61, 0.1);
        }
        
        .records-table tr:last-child td {
            border-bottom: none;
        }
        
        .records-table tr:hover {
            background-color: rgba(200, 155, 123, 0.05);
        }
        
        .no-records {
            text-align: center;
            padding: 30px;
            color: var(--royal-brown);
            opacity: 0.7;
        }
        
        .no-records i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
            color: var(--accent);
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
            .records-table {
                display: block;
                overflow-x: auto;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="dashboard.php" class="logo">
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
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="add_treatment.php"><i class="fas fa-plus-circle"></i> Add Treatment</a></li>
                    <li><a href="my_records.php" class="active"><i class="fas fa-heartbeat"></i> Health Records</a></li>
                    <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h1>My Health Records</h1>
                    <p>Review all the health records you've created for your patients.</p>
                </div>
                
                <!-- Records Section -->
                <div class="records-section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-heartbeat"></i> Treatment History</h3>
                    </div>
                    
                    <?php if ($records->num_rows > 0): ?>
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Pet</th>
                                <th>Owner</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($row['visit_date'])) ?></td>
                                <td><?= htmlspecialchars($row['pet_name']) ?></td>
                                <td><?= htmlspecialchars($row['owner_name']) ?></td>
                                <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                                <td><?= htmlspecialchars($row['treatment']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-records">
                        <i class="fas fa-file-medical-alt"></i>
                        <h3>No Health Records Found</h3>
                        <p>You haven't created any health records yet. Start by adding a treatment.</p>
                        <a href="add_treatment.php" class="btn btn-primary" style="margin-top: 15px;">Add Treatment</a>
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
</body>
</html>