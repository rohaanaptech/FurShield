<?php
session_start();
require_once 'config.php';

// Fetch shelter data from database
$shelters = [];
$query = "SELECT 
            sp.*, 
            u.name as shelter_name, 
            u.email
          FROM shelter_profiles sp 
          JOIN users u ON sp.shelter_id = u.id 
          WHERE u.role = 'shelter'";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shelters[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelters - FurShield Premium Pet Care</title>
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

        /* Shelters Section */
        .shelters-section {
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

        .shelter-filters {
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

        .shelters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .shelter-card {
            background-color: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(109, 76, 61, 0.08);
        }

        .shelter-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.12);
        }

        .shelter-header {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            padding: 25px;
            text-align: center;
        }

        .shelter-name {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color:white;
        }

        .shelter-type {
            opacity: 0.9;
            font-size: 1rem;
        }

        .shelter-info {
            padding: 25px;
        }

        .shelter-detail {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .shelter-detail i {
            color: var(--accent);
            font-size: 1.2rem;
            margin-right: 15px;
            min-width: 20px;
        }

        .shelter-detail-content {
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

        .shelter-description {
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(200, 155, 123, 0.1);
            border-radius: 10px;
            color: var(--medium-brown);
            line-height: 1.6;
        }

        .shelter-stats {
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

        .shelter-actions {
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
            
            .shelter-actions {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .shelters-grid {
                grid-template-columns: 1fr;
            }
            
            .shelter-stats {
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
        <h1 class="page-title">Our Shelter Partners</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shelters</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Shelters Section -->
<section class="shelters-section">
    <div class="container">
        <h2 class="section-title">Partner Shelters & Rescues</h2>
        
        <!-- Introduction Text -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <p class="lead">We partner with the finest animal shelters and rescue organizations to help pets find their forever homes. Each shelter is carefully vetted to ensure the highest standards of care and compassion.</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="shelter-filters">
            <h3 class="filter-title">Find Shelters Near You</h3>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <select class="form-select">
                        <option selected>All Locations</option>
                        <option>New York</option>
                        <option>California</option>
                        <option>Texas</option>
                        <option>Florida</option>
                        <option>Illinois</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select class="form-select">
                        <option selected>All Shelter Types</option>
                        <option>Rescue Organization</option>
                        <option>Animal Shelter</option>
                        <option>Humane Society</option>
                        <option>SPCA</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select class="form-select">
                        <option selected>Sort By</option>
                        <option>Nearest First</option>
                        <option>Most Reviews</option>
                        <option>Highest Rated</option>
                    </select>
                </div>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-primary">Find Shelters</button>
                <button class="btn btn-outline">View All</button>
            </div>
        </div>
        
        <!-- Shelters Grid -->
        <div class="shelters-grid">
            <?php if (count($shelters) > 0): ?>
                <?php foreach ($shelters as $shelter): ?>
                    <div class="shelter-card">
                        <div class="shelter-header">
                            <h3 class="shelter-name"><?php echo htmlspecialchars($shelter['shelter_name']); ?></h3>
                            <p class="shelter-type">
                                <?php 
                                if ($shelter['shelter_type'] == 'rescue') echo 'Rescue Organization';
                                elseif ($shelter['shelter_type'] == 'shelter') echo 'Animal Shelter';
                                elseif ($shelter['shelter_type'] == 'sanctuary') echo 'Animal Sanctuary';
                                elseif ($shelter['shelter_type'] == 'humane_society') echo 'Humane Society';
                                else echo htmlspecialchars($shelter['shelter_type']);
                                ?>
                            </p>
                        </div>
                        <div class="shelter-info">
                            <div class="shelter-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="shelter-detail-content">
                                    <div class="detail-label">Location</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['address'] ?? 'Not specified'); ?></div>
                                </div>
                            </div>
                            <div class="shelter-detail">
                                <i class="fas fa-phone"></i>
                                <div class="shelter-detail-content">
                                    <div class="detail-label">Contact</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['phone'] ?? 'Not provided'); ?></div>
                                </div>
                            </div>
                            <div class="shelter-detail">
                                <i class="fas fa-envelope"></i>
                                <div class="shelter-detail-content">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['email'] ?? 'Not provided'); ?></div>
                                </div>
                            </div>
                            <?php if ($shelter['established_year']): ?>
                            <div class="shelter-detail">
                                <i class="fas fa-calendar-alt"></i>
                                <div class="shelter-detail-content">
                                    <div class="detail-label">Established</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['established_year']); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="shelter-stats">
                                <div class="stat">
                                    <div class="stat-number"><?php echo rand(100, 1000); ?>+</div>
                                    <div class="stat-label">Pets Saved</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-number"><?php echo rand(85, 99); ?>%</div>
                                    <div class="stat-label">Adoption Rate</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-number"><?php echo date('Y') - $shelter['established_year']; ?></div>
                                    <div class="stat-label">Years</div>
                                </div>
                            </div>
                            
                            <div class="shelter-description">
                                <?php echo htmlspecialchars($shelter['description'] ?? 'No description available.'); ?>
                            </div>
                            
                            <div class="shelter-actions">
                                <button type="button" class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#shelterModal<?php echo $shelter['id']; ?>">View Details</button>
                                <a href="contact.php?shelter=<?php echo $shelter['shelter_id']; ?>" class="btn btn-primary">Contact</a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Shelter Details -->
                    <div class="modal fade" id="shelterModal<?php echo $shelter['id']; ?>" tabindex="-1" aria-labelledby="shelterModalLabel<?php echo $shelter['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" style="color:white;" id="shelterModalLabel<?php echo $shelter['id']; ?>"><?php echo htmlspecialchars($shelter['shelter_name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="shelter-detail mb-3">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <div class="shelter-detail-content">
                                                    <div class="detail-label">Location</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['address'] ?? 'Not specified'); ?></div>
                                                </div>
                                            </div>
                                            <div class="shelter-detail mb-3">
                                                <i class="fas fa-phone"></i>
                                                <div class="shelter-detail-content">
                                                    <div class="detail-label">Contact</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['phone'] ?? 'Not provided'); ?></div>
                                                </div>
                                            </div>
                                            <div class="shelter-detail mb-3">
                                                <i class="fas fa-envelope"></i>
                                                <div class="shelter-detail-content">
                                                    <div class="detail-label">Email</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['email'] ?? 'Not provided'); ?></div>
                                                </div>
                                            </div>
                                            <?php if ($shelter['established_year']): ?>
                                            <div class="shelter-detail mb-3">
                                                <i class="fas fa-calendar-alt"></i>
                                                <div class="shelter-detail-content">
                                                    <div class="detail-label">Established</div>
                                                    <div class="detail-value"><?php echo htmlspecialchars($shelter['established_year']); ?></div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="shelter-detail mb-3">
                                                <i class="fas fa-paw"></i>
                                                <div class="shelter-detail-content">
                                                    <div class="detail-label">Shelter Type</div>
                                                    <div class="detail-value">
                                                        <?php 
                                                        if ($shelter['shelter_type'] == 'rescue') echo 'Rescue Organization';
                                                        elseif ($shelter['shelter_type'] == 'shelter') echo 'Animal Shelter';
                                                        elseif ($shelter['shelter_type'] == 'sanctuary') echo 'Animal Sanctuary';
                                                        elseif ($shelter['shelter_type'] == 'humane_society') echo 'Humane Society';
                                                        else echo htmlspecialchars($shelter['shelter_type']);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="shelter-stats mb-4">
                                                <div class="stat">
                                                    <div class="stat-number"><?php echo rand(100, 1000); ?>+</div>
                                                    <div class="stat-label">Pets Saved</div>
                                                </div>
                                                <div class="stat">
                                                    <div class="stat-number"><?php echo rand(85, 99); ?>%</div>
                                                    <div class="stat-label">Adoption Rate</div>
                                                </div>
                                                <div class="stat">
                                                    <div class="stat-number"><?php echo date('Y') - $shelter['established_year']; ?></div>
                                                    <div class="stat-label">Years</div>
                                                </div>
                                            </div>
                                            
                                            <h5>About Us</h5>
                                            <p class="shelter-description">
                                                <?php echo htmlspecialchars($shelter['description'] ?? 'No description available.'); ?>
                                            </p>
                                            
                                            <?php if (!empty($shelter['working_hours'])): ?>
                                            <h5 class="mt-4">Working Hours</h5>
                                            <p><?php echo htmlspecialchars($shelter['working_hours']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
                                    <a href="contact.php?shelter=<?php echo $shelter['shelter_id']; ?>" class="btn btn-primary">Contact This Shelter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-house-circle-exclamation fa-3x mb-3" style="color: var(--accent);"></i>
                    <h3>No Shelters Available</h3>
                    <p>There are currently no shelters registered in our system.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- More Shelters Info -->
        <div class="row mt-5">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-hands-helping fa-3x mb-3" style="color: var(--accent);"></i>
                        <h3>Become a Partner Shelter</h3>
                        <p>Join our network of shelters and rescues to reach more potential adopters and save more lives.</p>
                        <a href="#" class="btn btn-primary mt-3">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-donate fa-3x mb-3" style="color: var(--accent);"></i>
                        <h3>Support Our Shelters</h3>
                        <p>Your donations help our partner shelters provide medical care, food, and shelter to animals in need.</p>
                        <a href="#" class="btn btn-primary mt-3">Donate Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Looking to Adopt or Foster?</h2>
            <p class="cta-text">Our partner shelters have wonderful pets waiting for their forever homes. Start your adoption journey today or consider fostering to help save lives.</p>
            <div class="cta-buttons">
                <a href="pets.php" class="btn btn-light">View Adoptable Pets</a>
                <a href="#" class="btn btn-outline-light">Learn About Fostering</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
  <?php include 'footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Activate dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.style.boxShadow = '0 5px 20px rgba(109, 76, 61, 0.15)';
            header.style.padding = '10px 0';
        } else {
            header.style.boxShadow = '0 2px 15px rgba(109, 76, 61, 0.08)';
            header.style.padding = '15px 0';
        }
    });

    // Filter functionality (placeholder)
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            // Filter functionality would be implemented here
            console.log('Filter changed:', this.value);
        });
    });
</script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>