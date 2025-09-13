<?php
session_start();

include 'config.php';
include 'auth.php';
requireOwner();

if ($_SESSION['role'] != 'owner') {
    die("❌ Only owners can view health records!");
}

$ownerId = $_SESSION['user_id'];
$records = $conn->query("
  SELECT h.id, h.visit_date, h.diagnosis, h.treatment,
         p.name AS pet_name, v.name AS vet_name
  FROM health_records h
  JOIN pets p ON h.pet_id = p.id
  JOIN users v ON h.vet_id = v.id
  WHERE p.owner_id = $ownerId
  ORDER BY h.visit_date DESC
");

$records_count = $records->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Records - FurShield</title>
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

        .health-container {
            background: var(--light);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .health-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-accent);
        }

        .health-title {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .health-count {
            background: var(--accent);
            color: var(--light);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }

        .health-table {
            width: 100%;
            border-collapse: collapse;
        }

        .health-table th {
            background: var(--light-accent);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--royal-brown);
            border-bottom: 2px solid var(--cream);
        }

        .health-table td {
            padding: 15px;
            border-bottom: 1px solid var(--light-accent);
            vertical-align: top;
        }

        .health-table tr:last-child td {
            border-bottom: none;
        }

        .health-table tr:hover {
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
            font-weight: 600;
            color: var(--royal-brown);
        }

        .visit-date {
            font-weight: 600;
            color: var(--royal-brown);
        }

        .diagnosis, .treatment {
            line-height: 1.5;
        }

        .diagnosis {
            color: #dc3545;
            font-weight: 500;
        }

        .treatment {
            color: var(--medium-brown);
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
            
            .health-table {
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
            
            .health-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                display: none;
            }
            
            .health-table th,
            .health-table td {
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
            
            .health-container {
                padding: 20px;
            }
            
            .health-table {
                font-size: 0.9rem;
            }
            
            .diagnosis, .treatment {
                font-size: 0.85rem;
            }
            
            .health-table th:nth-child(3),
            .health-table td:nth-child(3) {
                display: none;
            }
        }
    </style>
</head>
<body>
   <!-- header -->
   <?php 'header.php'?>

        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Health Records</h1>
            </div>

            <div class="health-container">
                <div class="health-header">
                    <h2 class="health-title">Medical History</h2>
                    <span class="health-count"><?php echo $records_count; ?> Record(s)</span>
                </div>

                <?php if ($records_count > 0): ?>
                    <div class="table-responsive">
                        <table class="health-table">
                            <thead>
                                <tr>
                                    <th>Pet</th>
                                    <th>Veterinarian</th>
                                    <th>Visit Date</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $records->fetch_assoc()): 
                                    $visit_date = new DateTime($row['visit_date']);
                                    $date_formatted = $visit_date->format('M j, Y');
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
                                            <div class="vet-info">Dr. <?php echo $row['vet_name']; ?></div>
                                        </td>
                                        <td>
                                            <div class="visit-date"><?php echo $date_formatted; ?></div>
                                        </td>
                                        <td>
                                            <div class="diagnosis"><?php echo $row['diagnosis'] ?: 'No diagnosis recorded'; ?></div>
                                        </td>
                                        <td>
                                            <div class="treatment"><?php echo $row['treatment'] ?: 'No treatment prescribed'; ?></div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3>No Health Records Found</h3>
                        <p class="empty-text">Your pets don't have any health records yet. Health records will appear here after veterinary visits.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php 'footer.php'?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>