<?php
session_start();
include '../config.php';

// Sirf vets ko access allow karo
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$vetId = $_SESSION['user_id'];

// Vet ke appointments nikaalo
$sql = "
  SELECT a.id, a.appointment_time, a.status,
         p.name AS pet_name, p.species AS pet_species, u.name AS owner_name
  FROM appointments a
  JOIN pets p ON a.pet_id = p.id
  JOIN users u ON a.owner_id = u.id
  WHERE a.vet_id = $vetId
  ORDER BY a.appointment_time ASC
";

$appointments = $conn->query($sql);

// Appointment counts for stats
$totalAppointments = $appointments->num_rows;
$pendingCount = 0;
$approvedCount = 0;
$cancelledCount = 0;
$completedCount = 0;


$appointments->data_seek(0);

// Count statuses
while($row = $appointments->fetch_assoc()) {
    switch($row['status']) {
        case 'Pending': $pendingCount++; break;
        case 'Approved': $approvedCount++; break;
        case 'Cancelled': $cancelledCount++; break;
        case 'Completed': $completedCount++; break;
    }
}

// Reset pointer again for display
$appointments->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurShield - My Appointments</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--light);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(109, 76, 61, 0.15);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .total .stat-icon { color: var(--info); }
        .pending .stat-icon { color: var(--warning); }
        .approved .stat-icon { color: var(--success); }
        .cancelled .stat-icon { color: var(--danger); }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--royal-brown);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--royal-brown);
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .appointments-table {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(109, 76, 61, 0.1);
        }
        
        th {
            font-weight: 600;
            color: var(--royal-brown);
            font-family: 'Playfair Display', serif;
        }
        
        tr:hover {
            background-color: rgba(200, 155, 123, 0.05);
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
            font-size: 1.2rem;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning);
        }
        
        .status-approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .status-cancelled {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }
        
        .status-completed {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--info);
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--royal-brown);
            color: var(--cream);
        }
        
        .btn-primary:hover {
            background-color: var(--accent);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #3d8b40;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d32f2f;
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
        
        .action-buttons {
            display: flex;
            gap: 8px;
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
            
            table {
                min-width: 800px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
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
                    <li><a href="appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="my_records.php"><i class="fas fa-heartbeat"></i> Health Records</a></li>
                    <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h2 class="page-title"><i class="fas fa-calendar-check"></i> My Appointments</h2>
                </div>
                
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-number"><?php echo $totalAppointments; ?></div>
                        <div class="stat-label">Total Appointments</div>
                    </div>
                    
                    <div class="stat-card pending">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $pendingCount; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    
                    <div class="stat-card approved">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $approvedCount; ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                    
                    <div class="stat-card cancelled">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $cancelledCount; ?></div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                </div>
                
                <!-- Appointments Table -->
                <div class="appointments-table">
                    <?php if ($appointments->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Owner</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="pet-info">
                                        <div class="pet-avatar">
                                            <i class="fas fa-<?php echo $row['pet_species'] == 'Dog' ? 'dog' : 'cat'; ?>"></i>
                                        </div>
                                        <div>
                                            <div><?php echo $row['pet_name']; ?></div>
                                            <div style="font-size: 0.8rem; opacity: 0.7;"><?php echo $row['pet_name']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['owner_name']; ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($row['status'] == 'Pending'): ?>
                                            <a href="update_status.php?id=<?= $row['id'] ?>&status=Approved" class="btn btn-success">Approve</a>
                                            <a href="update_status.php?id=<?= $row['id'] ?>&status=Cancelled" class="btn btn-danger">Cancel</a>
                                        <?php else: ?>
                                            <span style="font-style: italic; opacity: 0.7;">No action needed</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Appointments Found</h3>
                        <p>You don't have any appointments scheduled yet.</p>
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
            const statCards = document.querySelectorAll('.stat-card');
            const tableRows = document.querySelectorAll('tbody tr');
            
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
            
            // Animate table rows
            tableRows.forEach((row, index) => {
                row.style.opacity = 0;
                row.style.transform = 'translateX(-20px)';
                row.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    row.style.opacity = 1;
                    row.style.transform = 'translateX(0)';
                }, 300 + (index * 100));
            });
        });
    </script>
</body>
</html>