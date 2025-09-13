<?php
session_start();
require_once 'config.php';

// Redirect to login if trying to access message/appointment functionality without being logged in
if (isset($_GET['action']) && in_array($_GET['action'], ['message', 'appointment']) && !isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

// Fetch veterinarians from database
$doctors = [];
$query = "SELECT 
            vp.*, 
            u.name as doctor_name, 
            u.email
          FROM vet_profiles vp 
          JOIN users u ON vp.vet_id = u.id 
          WHERE u.role = 'vet'";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Check if user has pets
$userHasPets = false;
$userPets = [];
if (isset($_SESSION['user_id'])) {
    $checkPets = $conn->prepare("SELECT id, name FROM pets WHERE owner_id = ?");
    $checkPets->bind_param("i", $_SESSION['user_id']);
    $checkPets->execute();
    $petResult = $checkPets->get_result();
    if ($petResult->num_rows > 0) {
        $userHasPets = true;
        while ($pet = $petResult->fetch_assoc()) {
            $userPets[] = $pet;
        }
    }
    $checkPets->close();
}

// Handle appointment booking
// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to book appointments'); window.location.href = 'login.php';</script>";
        exit();
    }

    $owner_id = $_SESSION['user_id'];
    $vet_id   = intval($_POST['vet_id'] ?? 0);
    $date     = $_POST['appointment_date'] ?? '';
    $time     = $_POST['appointment_time'] ?? '';
    $reason   = $_POST['reason'] ?? null;
    $pet_id   = intval($_POST['pet_id'] ?? 0);

    // Check if pet belongs to logged-in user
    $checkPet = $conn->prepare("SELECT id FROM pets WHERE id = ? AND owner_id = ?");
    $checkPet->bind_param("ii", $pet_id, $owner_id);
    $checkPet->execute();
    $checkPet->store_result();

    if ($checkPet->num_rows === 0) {
        $appointment_error = "Invalid pet selection.";
    } elseif ($vet_id && $date && $time) {
        $appointment_time = $date . ' ' . $time;
        $status = "Pending";

        $query = "INSERT INTO appointments (pet_id, owner_id, vet_id, appointment_time, notes, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiisss", $pet_id, $owner_id, $vet_id, $appointment_time, $reason, $status);

        if ($stmt->execute()) {
            $appointment_success = "Appointment booked successfully!";
        } else {
            $appointment_error = "Error booking appointment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $appointment_error = "Please fill in all required fields.";
    }
    $checkPet->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarians - FurShield Premium Pet Care</title>
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
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--cream);
            color: var(--royal-brown);
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--royal-brown);
        }

        /* Page Header */
        .page-header {
            padding: 150px 0 80px;
            background: linear-gradient(to right, rgba(248, 244, 233, 0.9), rgba(248, 244, 233, 0.7)), url('https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 3.5rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }

        .breadcrumb {
            justify-content: center;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--royal-brown);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--accent);
        }

        /* Doctors Section */
        .doctors-section {
            padding: 40px 0 80px;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }

        .doctor-filters {
            background-color: var(--light);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            margin-bottom: 40px;
        }

        .filter-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--royal-brown);
        }

        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .doctor-card {
            background-color: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(109, 76, 61, 0.08);
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.12);
        }

        .doctor-header {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .doctor-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--cream);
            margin: 0 auto 15px;
            background-color: var(--light-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--royal-brown);
        }

        .doctor-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .doctor-name {
            font-size: 1.8rem;
            margin-bottom: 5px;
            color: white;
        }

        .doctor-specialization {
            opacity: 0.9;
            font-size: 1rem;
        }

        .doctor-info {
            padding: 25px;
        }

        .doctor-detail {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .doctor-detail i {
            color: var(--accent);
            font-size: 1.2rem;
            margin-right: 15px;
            min-width: 20px;
        }

        .doctor-detail-content {
            flex: 1;
        }

        .detail-label {
            font-weight: 600;
            color: var(--royal-brown);
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--medium-brown);
        }

        .doctor-description {
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(200, 155, 123, 0.1);
            border-radius: 10px;
            color: var(--medium-brown);
            line-height: 1.6;
        }

        .doctor-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            border-top: 1px solid var(--light-accent);
            border-bottom: 1px solid var(--light-accent);
            padding: 15px 0;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--medium-brown);
        }

        .doctor-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            border-bottom: none;
            padding: 25px;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: white;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid var(--light-accent);
            padding: 15px 25px;
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(to right, rgba(109, 76, 61, 0.9), rgba(200, 155, 123, 0.85)), url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            color: var(--cream);
            border-radius: 40px;
            margin: 40px 0;
            text-align: center;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 2.8rem;
            margin-bottom: 20px;
            color: var(--cream);
        }

        .cta-text {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.8;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-light {
            background-color: var(--cream);
            color: var(--royal-brown);
            border: 2px solid var(--cream);
            box-shadow: 0 4px 15px rgba(248, 244, 233, 0.3);
        }

        .btn-light:hover {
            background-color: transparent;
            color: var(--cream);
            box-shadow: none;
        }

        .btn-outline-light {
            border: 2px solid var(--cream);
            color: var(--cream);
            background-color: transparent;
        }

        .btn-outline-light:hover {
            background-color: var(--cream);
            color: var(--royal-brown);
        }

        /* Alert Styles */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .page-title {
                font-size: 2.8rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta-title {
                font-size: 2.2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .doctor-actions {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            
            .doctor-stats {
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .stat {
                flex: 1;
                min-width: 100px;
            }
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            background-color: var(--cream);
            border: 2px solid var(--royal-brown);
            border-radius: 10px;
            padding: 8px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            min-width: 200px;
        }

        .dropdown-item {
            color: var(--royal-brown);
            font-weight: 500;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--royal-brown);
            color: var(--cream);
        }

        .dropdown-divider {
            border-top: 2px solid var(--light-accent);
            margin: 5px 0;
        }

        .btn-outline.dropdown-toggle {
            border: 2px solid var(--royal-brown);
            color: var(--royal-brown);
            background-color: transparent;
        }

        .btn-outline.dropdown-toggle:hover,
        .btn-outline.dropdown-toggle:focus {
            background-color: var(--royal-brown);
            color: var(--cream);
        }
    </style>
</head>
<body>
<!-- Header -->
<?php include 'header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Veterinarians</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Veterinarians</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Doctors Section -->
<section class="doctors-section">
    <div class="container">
        <h2 class="section-title">Meet Our Expert Veterinarians</h2>
        
        <!-- Filters -->
        <div class="doctor-filters">
            <h3 class="filter-title">Find the Right Veterinarian</h3>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" placeholder="Search by name..." id="searchInput">
                </div>
                <div class="col-md-4 mb-3">
                    <select class="form-select" id="specializationFilter">
                        <option value="">All Specializations</option>
                        <option value="Surgery">Surgery</option>
                        <option value="Dentistry">Dentistry</option>
                        <option value="Dermatology">Dermatology</option>
                        <option value="Internal Medicine">Internal Medicine</option>
                        <option value="Emergency Care">Emergency Care</option>
                        <option value="Behavior">Behavior</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select class="form-select" id="experienceFilter">
                        <option value="">Any Experience Level</option>
                        <option value="5">5+ Years</option>
                        <option value="10">10+ Years</option>
                        <option value="15">15+ Years</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Doctors Grid -->
        <div class="doctors-grid" id="doctorsGrid">
            <?php if(count($doctors) > 0): ?>
                <?php foreach($doctors as $doctor): ?>
                <div class="doctor-card" data-specialization="<?php echo htmlspecialchars($doctor['specialization'] ?? ''); ?>" data-experience="<?php echo htmlspecialchars($doctor['experience'] ?? 0); ?>">
                    <div class="doctor-header">
                        <div class="doctor-avatar">
                            <?php if (!empty($doctor['profile_image'])): ?>
                                <img src="uploads/profiles/<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="<?php echo htmlspecialchars($doctor['doctor_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-user-md"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="doctor-name"><?php echo htmlspecialchars($doctor['doctor_name']); ?></h3>
                        <p class="doctor-specialization"><?php echo htmlspecialchars($doctor['specialization'] ?? 'Veterinarian'); ?></p>
                    </div>
                    <div class="doctor-info">
                        <div class="doctor-detail">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="doctor-detail-content">
                                <div class="detail-label">Qualification</div>
                                <div class="detail-value"><?php echo htmlspecialchars($doctor['qualification'] ?? 'DVM'); ?></div>
                            </div>
                        </div>
                        <div class="doctor-detail">
                            <i class="fas fa-briefcase"></i>
                            <div class="doctor-detail-content">
                                <div class="detail-label">Experience</div>
                                <div class="detail-value"><?php echo htmlspecialchars($doctor['experience'] ?? 0); ?>+ years</div>
                            </div>
                        </div>
                        <div class="doctor-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="doctor-detail-content">
                                <div class="detail-label">Clinic Address</div>
                                <div class="detail-value"><?php echo htmlspecialchars($doctor['clinic_address'] ?? $doctor['address'] ?? 'Not specified'); ?></div>
                            </div>
                        </div>
                     
                        <div class="doctor-stats">
                            <div class="stat">
                                <div class="stat-number"><?php echo rand(100, 500); ?></div>
                                <div class="stat-label">Patients</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number"><?php echo rand(4, 5); ?>.<?php echo rand(0, 9); ?></div>
                                <div class="stat-label">Rating</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number"><?php echo rand(95, 100); ?>%</div>
                                <div class="stat-label">Satisfaction</div>
                            </div>
                        </div>
                        
                        <div class="doctor-actions">
                            <!-- Appointment Button with proper validation -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($userHasPets): ?>
                                    <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#appointmentModal" data-doctor-id="<?php echo $doctor['vet_id']; ?>" data-doctor-name="<?php echo htmlspecialchars($doctor['doctor_name']); ?>">
                                        <i class="fas fa-calendar-check me-2"></i>Book Appointment
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline" onclick="alert('You must register a pet before booking an appointment.'); window.location.href='add_pet.php';">
                                        <i class="fas fa-calendar-check me-2"></i>Book Appointment
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-outline" onclick="window.location.href='login.php?action=appointment'">
                                    <i class="fas fa-calendar-check me-2"></i>Book Appointment
                                </button>
                            <?php endif; ?>
                            
                            <!-- Message Button with proper validation -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messageModal" data-doctor-id="<?php echo $doctor['vet_id']; ?>" data-doctor-name="<?php echo htmlspecialchars($doctor['doctor_name']); ?>">
                                    <i class="fas fa-envelope me-2"></i>Message
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" onclick="window.location.href='login.php?action=message'">
                                    <i class="fas fa-envelope me-2"></i>Message
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-user-md fa-3x mb-3" style="color: var(--accent);"></i>
                    <h3>No Veterinarians Available</h3>
                    <p>Check back later for our veterinary professionals.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Need Expert Care for Your Pet?</h2>
            <p class="cta-text">Our veterinarians are here to provide the best care for your furry family members. Book an appointment today and ensure your pet's health and happiness.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-light">Contact Us</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-outline-light">Create Account</a>
                <?php elseif (!$userHasPets): ?>
                    <a href="add_pet.php" class="btn btn-outline-light">Add a Pet</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?>

<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Veterinarian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="messageForm">
                <div class="modal-body">
                    <input type="hidden" name="vet_id" id="messageDoctorId">
                    <input type="hidden" name="send_message" value="1">
                    
                    <?php if (isset($message_success)): ?>
                        <div class="alert alert-success"><?php echo $message_success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($message_error)): ?>
                        <div class="alert alert-danger"><?php echo $message_error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="messageDoctorName" class="form-label">To</label>
                        <input type="text" class="form-control" id="messageDoctorName" name="doctor_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="messageSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="messageSubject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message</label>
                        <textarea class="form-control" id="messageContent" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="appointmentForm">
                <div class="modal-body">
                    <input type="hidden" name="vet_id" id="appointmentDoctorId">
                    <input type="hidden" name="book_appointment" value="1">
                    
                    <?php if (isset($appointment_success)): ?>
                        <div class="alert alert-success"><?php echo $appointment_success; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($appointment_error)): ?>
                        <div class="alert alert-danger"><?php echo $appointment_error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="doctorName" class="form-label">Veterinarian</label>
                        <input type="text" class="form-control" id="doctorName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="petSelect" class="form-label">Select Pet *</label>
                        <select class="form-select" id="petSelect" name="pet_id" required>
                            <option value="">Choose your pet</option>
                            <?php if (!empty($userPets)): ?>
                                <?php foreach ($userPets as $pet): ?>
                                    <option value="<?php echo $pet['id']; ?>">
                                        <?php echo htmlspecialchars($pet['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No pets registered</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointmentDate" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="appointmentDate" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="appointmentTime" class="form-label">Time *</label>
                            <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Visit</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const specializationFilter = document.getElementById('specializationFilter');
        const experienceFilter = document.getElementById('experienceFilter');
        const doctorCards = document.querySelectorAll('.doctor-card');
        
        function filterDoctors() {
            const searchText = searchInput.value.toLowerCase();
            const specialization = specializationFilter.value;
            const experience = experienceFilter.value;
            
            doctorCards.forEach(card => {
                const name = card.querySelector('.doctor-name').textContent.toLowerCase();
                const cardSpecialization = card.getAttribute('data-specialization');
                const cardExperience = parseInt(card.getAttribute('data-experience'));
                
                const matchesSearch = name.includes(searchText);
                const matchesSpecialization = !specialization || cardSpecialization === specialization;
                const matchesExperience = !experience || cardExperience >= parseInt(experience);
                
                if (matchesSearch && matchesSpecialization && matchesExperience) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterDoctors);
        specializationFilter.addEventListener('change', filterDoctors);
        experienceFilter.addEventListener('change', filterDoctors);
        
        // Appointment Modal
        const appointmentModal = document.getElementById('appointmentModal');
        if (appointmentModal) {
            appointmentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const doctorId = button.getAttribute('data-doctor-id');
                const doctorName = button.getAttribute('data-doctor-name');
                
                document.getElementById('appointmentDoctorId').value = doctorId;
                document.getElementById('doctorName').value = doctorName;
                
                // Clear any previous messages
                const alerts = appointmentModal.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            });
        }
        
        // Message Modal
        const messageModal = document.getElementById('messageModal');
        if (messageModal) {
            messageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const doctorId = button.getAttribute('data-doctor-id');
                const doctorName = button.getAttribute('data-doctor-name');
                
                document.getElementById('messageDoctorId').value = doctorId;
                document.getElementById('messageDoctorName').value = doctorName;
                
                // Clear any previous messages
                const alerts = messageModal.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            });
        }
        
        // Set minimum date for appointment to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('appointmentDate').setAttribute('min', today);
    });
</script>
</body>
</html>