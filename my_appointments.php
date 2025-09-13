<?php
session_start();
include 'config.php';
include 'auth.php';
requireOwner();

if ($_SESSION['role'] != 'owner') {
    die("❌ Only owners can view appointments!");
}

$ownerId = $_SESSION['user_id'];

$appointments = $conn->query("
  SELECT a.id, a.appointment_time, a.status,
         p.name AS pet_name, 
         v.name AS vet_name, v.email AS vet_email,
         vp.qualification, vp.specialization
  FROM appointments a
  JOIN pets p ON a.pet_id = p.id
  JOIN users v ON a.vet_id = v.id
  LEFT JOIN vet_profiles vp ON v.id = vp.vet_id
  WHERE a.owner_id = $ownerId
  ORDER BY a.appointment_time ASC
");

$appointments_count = $appointments->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - FurShield</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --gold: #d4af37;
            --medium-brown: #8a7365;
            --shadow: 0 10px 30px rgba(109, 76, 61, 0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
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

        .book-appointment-btn {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .book-appointment-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.3);
            color: var(--light);
        }

        .book-appointment-btn i {
            margin-right: 8px;
        }

        .appointments-container {
            background: var(--light);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .appointments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-accent);
        }

        .appointments-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .appointments-count {
            background: var(--accent);
            color: var(--light);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointments-table th {
            background: var(--light-accent);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--royal-brown);
            border-bottom: 2px solid var(--cream);
        }

        .appointments-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light-accent);
            vertical-align: top;
        }

        .appointments-table tr:last-child td {
            border-bottom: none;
        }

        .appointments-table tr:hover {
            background: rgba(200, 155, 123, 0.05);
        }

        .pet-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pet-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--royal-brown));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            font-size: 1.2rem;
        }

        .vet-info {
            line-height: 1.5;
        }

        .vet-name {
            font-weight: 600;
            color: var(--royal-brown);
        }

        .vet-email {
            color: var(--medium-brown);
            font-size: 0.9rem;
        }

        .vet-qualification {
            margin-top: 5px;
            font-size: 0.9rem;
        }

        .appointment-time {
            font-weight: 600;
            color: var(--royal-brown);
        }

        .appointment-date {
            color: var(--medium-brown);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--light-accent);
            margin-bottom: 20px;
        }

        .empty-text {
            color: var(--medium-brown);
            margin-bottom: 30px;
            font-size: 1.1rem;
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
            
            .appointments-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .appointments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .appointments-table th,
            .appointments-table td {
                padding: 10px;
            }
            
            .pet-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .pet-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 20px;
            }
            
            .appointments-container {
                padding: 20px;
            }
            
            .appointments-table {
                font-size: 0.9rem;
            }
            
            .vet-info,
            .vet-qualification {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include 'header.php'; ?>

   
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">My Appointments</h1>
                <a href="book_appointment.php" class="book-appointment-btn">
                    <i class="fas fa-calendar-plus"></i> Book New Appointment
                </a>
            </div>

            <div class="appointments-container">
                <div class="appointments-header">
                    <h2 class="appointments-title">Scheduled Appointments</h2>
                    <span class="appointments-count"><?php echo $appointments_count; ?> Appointment(s)</span>
                </div>

                <?php if ($appointments_count > 0): ?>
                    <div class="table-responsive">
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Pet</th>
                                    <th>Veterinarian</th>
                                    <th>Qualification & Specialization</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $appointments->fetch_assoc()): 
                                    $appointment_time = new DateTime($row['appointment_time']);
                                    $date_formatted = $appointment_time->format('M j, Y');
                                    $time_formatted = $appointment_time->format('g:i A');
                                    
                                    $status_class = '';
                                    switch($row['status']) {
                                        case 'Pending':
                                            $status_class = 'status-pending';
                                            break;
                                        case 'Approved':
                                            $status_class = 'status-approved';
                                            break;
                                        case 'Cancelled':
                                            $status_class = 'status-cancelled';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="pet-info">
                                                <div class="pet-icon">
                                                    <i class="fas fa-paw"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $row['pet_name']; ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="vet-info">
                                                <div class="vet-name">Dr. <?php echo $row['vet_name']; ?></div>
                                                <div class="vet-email"><?php echo $row['vet_email']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="vet-qualification">
                                                <div><strong><?php echo $row['qualification'] ?? 'Veterinarian'; ?></strong></div>
                                                <div><?php echo $row['specialization'] ?? 'General Practice'; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="appointment-time"><?php echo $time_formatted; ?></div>
                                            <div class="appointment-date"><?php echo $date_formatted; ?></div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3>No Appointments Yet</h3>
                        <p class="empty-text">You haven't scheduled any appointments yet. Book your first appointment to get started!</p>
                        <a href="book_appointment.php" class="book-appointment-btn">
                            <i class="fas fa-calendar-plus"></i> Book Your First Appointment
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>