<?php
session_start();
require_once('config.php');

$petSource = 'owner';

// Base query for owner pets
$sql = "SELECT * FROM adoptable_pets ";

// Filters
if (!empty($_GET['species'])) {
    $species = $conn->real_escape_string($_GET['species']);
    $sql .= " AND species = '$species'";
}
if (!empty($_GET['breed'])) {
    $breed = $conn->real_escape_string($_GET['breed']);
    $sql .= " AND breed LIKE '%$breed%'";
}
if (!empty($_GET['gender'])) {
    $gender = $conn->real_escape_string($_GET['gender']);
    $sql .= " AND gender = '$gender'";
}
if (!empty($_GET['size'])) {
    $size = $conn->real_escape_string($_GET['size']);
    $sql .= " AND size = '$size'";
}
if (!empty($_GET['age_range'])) {
    $age_range = $_GET['age_range'];
    if ($age_range == "0-1") {
        $sql .= " AND age >= 0 AND age <= 1";
    } elseif ($age_range == "1-3") {
        $sql .= " AND age > 1 AND age <= 3";
    } elseif ($age_range == "3-8") {
        $sql .= " AND age > 3 AND age <= 8";
    } elseif ($age_range == "8+") {
        $sql .= " AND age >= 8";
    }
}

// Ordering
$sql .= " ORDER BY created_at DESC";

// Run query
$result = $conn->query($sql);
$pets = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pets[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Pets - FurShield Premium Pet Care</title>
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
            background: linear-gradient(to right, rgba(248, 244, 233, 0.9), rgba(248, 244, 233, 0.7)), url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
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

        /* Pets Section */
        .pets-section {
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

        .pet-filters {
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

        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .pet-card {
            background-color: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(109, 76, 61, 0.08);
        }

        .pet-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.12);
        }

        .pet-image {
            height: 250px;
            overflow: hidden;
        }

        .pet-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .pet-card:hover .pet-image img {
            transform: scale(1.05);
        }

        .pet-info {
            padding: 25px;
        }

        .pet-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--royal-brown);
        }

        .pet-breed {
            color: var(--medium-brown);
            margin-bottom: 15px;
            font-weight: 500;
        }

        .pet-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--light-accent);
            padding-bottom: 15px;
        }

        .pet-detail {
            text-align: center;
        }

        .detail-value {
            font-weight: 600;
            color: var(--royal-brown);
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--medium-brown);
        }

        .pet-description {
            margin-bottom: 20px;
            color: var(--medium-brown);
            line-height: 1.6;
        }

        .pet-actions {
            display: flex;
            justify-content: space-between;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }

        .page-link {
            color: var(--royal-brown);
            border: 1px solid var(--light-accent);
            padding: 10px 18px;
            margin: 0 5px;
            border-radius: 10px;
        }

        .page-link:hover {
            background-color: var(--royal-brown);
            color: var(--cream);
            border-color: var(--royal-brown);
        }

        .page-item.active .page-link {
            background-color: var(--royal-brown);
            border-color: var(--royal-brown);
            color: var(--cream);
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
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .pet-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .pet-actions .btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Status badges */
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .status-available {
            background-color: #28a745;
            color: white;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: black;
        }
        
        .status-adopted {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
<!-- Include Header -->
<?php include('header.php'); ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Our Furry Friends</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pets</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Pets Section -->

<section class="pets-section">
    <div class="container">
        <h2 class="section-title">Meet Our Pets</h2>
        
        <!-- Filters -->
        <div class="pet-filters">
            <h3 class="filter-title">Find Your Perfect Companion</h3>
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select class="form-select" name="species">
                            <option value="">All Species</option>
                            <?php
                            // Get unique species from database
                            $species_sql = "SELECT DISTINCT species FROM adoptable_pets WHERE species IS NOT NULL ORDER BY species";
                            $species_result = $conn->query($species_sql);
                            while($species = $species_result->fetch_assoc()) {
                                $selected = (isset($_GET['species']) && $_GET['species'] == $species['species']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($species['species']) . '" ' . $selected . '>' . htmlspecialchars($species['species']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select class="form-select" name="breed">
                            <option value="">All Breeds</option>
                            <?php
                            // Get unique breeds from database
                            $breed_sql = "SELECT DISTINCT breed FROM adoptable_pets WHERE breed IS NOT NULL ORDER BY breed";
                            $breed_result = $conn->query($breed_sql);
                            while($breed = $breed_result->fetch_assoc()) {
                                $selected = (isset($_GET['breed']) && $_GET['breed'] == $breed['breed']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($breed['breed']) . '" ' . $selected . '>' . htmlspecialchars($breed['breed']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="age_range">
                            <option value="">Any Age</option>
                            <option value="0-1" <?php echo (isset($_GET['age_range']) && $_GET['age_range'] == '0-1') ? 'selected' : ''; ?>>Baby (0-1 yr)</option>
                            <option value="1-3" <?php echo (isset($_GET['age_range']) && $_GET['age_range'] == '1-3') ? 'selected' : ''; ?>>Young (1-3 yrs)</option>
                            <option value="3-8" <?php echo (isset($_GET['age_range']) && $_GET['age_range'] == '3-8') ? 'selected' : ''; ?>>Adult (3-8 yrs)</option>
                            <option value="8+" <?php echo (isset($_GET['age_range']) && $_GET['age_range'] == '8+') ? 'selected' : ''; ?>>Senior (8+ yrs)</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="gender">
                            <option value="">Any Gender</option>
                            <option value="male" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="size">
                            <option value="">Any Size</option>
                            <option value="small" <?php echo (isset($_GET['size']) && $_GET['size'] == 'small') ? 'selected' : ''; ?>>Small</option>
                            <option value="medium" <?php echo (isset($_GET['size']) && $_GET['size'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="large" <?php echo (isset($_GET['size']) && $_GET['size'] == 'large') ? 'selected' : ''; ?>>Large</option>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="pets.php" class="btn btn-outline">Reset Filters</a>
                </div>
            </form>
        </div>
        
       <!-- Pets Grid -->
<div class="pets-grid">
    <?php if (count($pets) > 0): ?>
        <?php foreach ($pets as $pet): ?>
            <div class="pet-card">
                <div class="pet-image">
                    <span class="status-badge status-<?php echo strtolower($pet['status']); ?>">
                        <?php echo $pet['status']; ?>
                    </span>
                    <img src="../<?php echo !empty($pet['photo'])   ? htmlspecialchars($pet['photo']): 'uploads/pets/placeholder.jpg'; ?>" 
                    alt="<?php echo htmlspecialchars($pet['pet_name']); ?>">

                </div>
                <div class="pet-info">
                    <h3 class="pet-name"><?php echo htmlspecialchars($pet['pet_name']); ?></h3>
                    <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></p>
                    <div class="pet-details">
                        <div class="pet-detail">
                            <div class="detail-value"><?php echo htmlspecialchars($pet['age']); ?> years</div>
                            <div class="detail-label">Age</div>
                        </div>
                        <div class="pet-detail">
                            <div class="detail-value"><?php echo ucfirst(htmlspecialchars($pet['gender'])); ?></div>
                            <div class="detail-label">Gender</div>
                        </div>
                        <div class="pet-detail">
                            <div class="detail-value"><?php echo ucfirst(htmlspecialchars($pet['size'])); ?></div>
                            <div class="detail-label">Size</div>
                        </div>
                    </div>
                    <p class="pet-description">
                        <?php echo !empty($pet['description']) 
                            ? htmlspecialchars(substr($pet['description'], 0, 100)) . '...' 
                            : 'Loving companion looking for a forever home.'; ?>
                    </p>
                    <div class="pet-actions">
                        <a href="adopt.php?pet_id=<?php echo $pet['id']; ?>&type=<?php echo $petSource; ?>" 
                           class="btn btn-primary">Adopt Me</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <h3>No pets available for adoption at the moment.</h3>
        </div>
    <?php endif; ?>
</div>
        <!-- Pagination - Only show if there are more than 6 pets -->
        <?php if (count($pets) > 6): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<!-- Pet Modal -->
<div class="modal fade" id="petModal" tabindex="-1" aria-labelledby="petModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="petModalLabel">Pet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="petModalBody">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="adoptBtn">Adopt Me</a>
            </div>
        </div>
    </div>
</div>

<script>
// Function to handle pet details modal
document.addEventListener('DOMContentLoaded', function() {
    const petModal = document.getElementById('petModal');
    const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
    
    petModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const petId = button.getAttribute('data-pet-id');
        const modalBody = document.getElementById('petModalBody');
        const adoptBtn = document.getElementById('adoptBtn');
        
        // Show loading spinner
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        // Fetch pet details via AJAX
        fetch('get_pet_details.php?id=' + petId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pet = data.pet;
                    modalBody.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${pet.photo || 'https://images.unsplash.com/photo-1554692918-08fa0fdc9db3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'}" 
                                     alt="${pet.pet_name}" class="img-fluid rounded">
                                <div class="mt-3 text-center">
                                    <span class="badge bg-${pet.status === 'Available' ? 'success' : pet.status === 'Pending' ? 'warning' : 'secondary'}">${pet.status}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>${pet.pet_name}</h3>
                                <p><strong>Breed:</strong> ${pet.breed}</p>
                                <p><strong>Age:</strong> ${pet.age} years</p>
                                <p><strong>Gender:</strong> ${pet.gender}</p>
                                <p><strong>Size:</strong> ${pet.size}</p>
                                <p><strong>Species:</strong> ${pet.species}</p>
                                <hr>
                                <h5>About</h5>
                                <p>${pet.description || 'Loving companion looking for a forever home.'}</p>
                            </div>
                        </div>
                    `;
                    
                    // Update adopt button
                    adoptBtn.href = 'adopt.php?pet_id=' + petId;
                } else {
                    modalBody.innerHTML = '<div class="alert alert-danger">Error loading pet details.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<div class="alert alert-danger">Error loading pet details.</div>';
            });
    });
});
</script>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Can't Find Your Perfect Companion?</h2>
            <p class="cta-text">We have many more pets waiting for their forever homes. Contact us to schedule a visit or learn more about our adoption process.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-light">Contact Us</a>
                <a href="#" class="btn btn-outline-light">Adoption Process</a>
            </div>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Animation for pet cards on scroll
    const petCards = document.querySelectorAll('.pet-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    petCards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(50px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
</script>
</body>
</html>