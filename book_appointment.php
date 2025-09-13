<?php
session_start();
include 'config.php';
include 'auth.php';
requireOwner(); 

if ($_SESSION['role'] != 'owner') {
    die("❌ Only owners can book appointments!");
}

$ownerId = $_SESSION['user_id'];

// Pets load
$pets = $conn->query("SELECT * FROM pets WHERE owner_id=$ownerId");

// Vets load with profile
$vets = $conn->query("
    SELECT u.id, u.name, u.email, vp.qualification, vp.specialization
    FROM users u
    LEFT JOIN vet_profiles vp ON u.id = vp.vet_id
    WHERE u.role='vet'
");

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['pet_id']) && !empty($_POST['vet_id'])) {
        $pet_id = intval($_POST['pet_id']);
        $vet_id = intval($_POST['vet_id']);
        $appt_time = $_POST['appointment_time'];
        $notes = $_POST['notes'] ?? '';

        $stmt = $conn->prepare("INSERT INTO appointments (pet_id, owner_id, vet_id, appointment_time, notes) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iiiss", $pet_id, $ownerId, $vet_id, $appt_time, $notes);

        if ($stmt->execute()) {
            $success_message = "✅ Appointment booked successfully!";
        } else {
            $error_message = "❌ Error: " . $stmt->error;
        }
    } else {
        $error_message = "⚠️ Please select both a pet and a vet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - FurShield</title>
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

        .vet-card {
            background: var(--cream);
            border: 2px solid var(--light-accent);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .vet-card:hover {
            border-color: var(--accent);
            background-color: var(--light);
        }

        .vet-name {
            font-weight: 600;
            color: var(--royal-brown);
            margin-bottom: 5px;
        }

        .vet-details {
            color: var(--medium-brown);
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--light);
            border-radius: 20px;
            box-shadow: var(--shadow);
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

        .add-pet-btn {
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

        .add-pet-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.3);
            color: var(--light);
        }

        .add-pet-btn i {
            margin-right: 8px;
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
        }
    </style>
</head>
<body>
 <?php include 'header.php'; ?>

            <?php if ($pets->num_rows > 0): ?>
                <div class="form-container">
                    <h2 class="form-title">Schedule a New Appointment</h2>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="message success">
                            <?php echo $success_message; ?> 
                            <a href="my_appointments.php" style="color: #2d8045; text-decoration: underline; margin-left: 10px;">View Appointments</a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="message error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label class="form-label" for="pet_id"><i class="fas fa-paw"></i> Select Pet</label>
                            <select class="form-control" id="pet_id" name="pet_id" required>
                                <option value="">Choose your pet</option>
                                <?php 
                                $pets->data_seek(0);
                                while($p = $pets->fetch_assoc()): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?> (<?= $p['species'] ?> - <?= $p['breed'] ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="vet_id"><i class="fas fa-user-md"></i> Select Veterinarian</label>
                            <select class="form-control" id="vet_id" name="vet_id" required>
                                <option value="">Choose a veterinarian</option>
                                <?php 
                                $vets->data_seek(0);
                                while($v = $vets->fetch_assoc()): ?>
                                    <option value="<?= $v['id'] ?>">
                                        Dr. <?= $v['name'] ?> - <?= $v['qualification'] ?? 'Veterinarian' ?> (<?= $v['specialization'] ?? 'General Practice' ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="appointment_time"><i class="fas fa-calendar-alt"></i> Appointment Date & Time</label>
                            <input type="datetime-local" class="form-control" id="appointment_time" name="appointment_time" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="notes"><i class="fas fa-sticky-note"></i> Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" placeholder="Please describe the reason for the appointment or any special requirements"></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <h3>No Pets Found</h3>
                    <p class="empty-text">You need to add a pet before you can book an appointment.</p>
                    <a href="add_pet.php" class="add-pet-btn">
                        <i class="fas fa-plus-circle"></i> Add Your First Pet
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().slice(0, 16);
            document.getElementById('appointment_time').min = today;
        });
    </script>
</body>
</html>