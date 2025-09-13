<?php
session_start();
require_once 'config.php';

$testimonials = [];
$query = "SELECT name, subject, message, created_at FROM contacts ";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

// If no testimonials in database, use these custom ones
$customTestimonials = [
    [
        'name' => 'Sarah Johnson',
        'subject' => 'Excellent Pet Care Service',
        'message' => 'FurShield has been a lifesaver for my three dogs. The vaccination reminders are incredibly helpful, and the vet booking system is so convenient!',
        'created_at' => '2023-10-15 14:30:00'
    ],
    [
        'name' => 'Michael Chen',
        'subject' => 'Amazing Platform for Pet Owners',
        'message' => 'As a veterinarian, I appreciate how FurShield streamlines appointments and gives me quick access to pet medical histories. Highly recommended!',
        'created_at' => '2023-09-22 11:45:00'
    ],
    [
        'name' => 'Emily Rodriguez',
        'subject' => 'Best Decision for My Shelter',
        'message' => 'We\'ve found forever homes for 30% more pets since we started using FurShield\'s adoption portal. This platform is a game-changer for animal shelters!',
        'created_at' => '2023-11-05 09:15:00'
    ]
];

// Use custom testimonials if none in database
if (empty($testimonials)) {
    $testimonials = $customTestimonials;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - FurShield Premium Pet Care</title>
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
            background: linear-gradient(to right, rgba(248, 244, 233, 0.9), rgba(248, 244, 233, 0.7)), url('https://images.unsplash.com/photo-1544568100-847a948585b9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
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

        /* Testimonials Section */
        .testimonials-section {
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

        .testimonial-filters {
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

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .testimonial-card {
            background-color: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(109, 76, 61, 0.08);
            position: relative;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.12);
        }

        .testimonial-header {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .testimonial-quote {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.2);
            font-family: 'Playfair Display', serif;
        }

        .testimonial-name {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .testimonial-subject {
            opacity: 0.9;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .testimonial-date {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .testimonial-body {
            padding: 25px;
            position: relative;
        }

        .testimonial-text {
            color: var(--medium-brown);
            line-height: 1.8;
            margin-bottom: 20px;
            font-style: italic;
            position: relative;
            padding-left: 20px;
            border-left: 3px solid var(--light-accent);
        }

        .testimonial-rating {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
        }

        .testimonial-rating i {
            color: var(--accent);
        }

        .testimonial-pet-type {
            display: inline-block;
            background-color: var(--light-accent);
            color: var(--royal-brown);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(to right, var(--royal-brown), var(--accent));
            color: var(--cream);
            padding: 80px 0;
            border-radius: 30px;
            margin: 60px 0;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
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

        /* Review Form */
        .review-form {
            background-color: var(--light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.08);
            margin: 60px 0;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
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
            
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .review-form {
                padding: 25px;
            }
        }

       
    </style>
</head>
<body>
<!-- Header -->
<?php include 'header.php'?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Customer Testimonials</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Testimonials</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title">What Our Customers Say</h2>
        
        <!-- Introduction Text -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <p class="lead">At FurShield, we take pride in providing exceptional care for your beloved pets. But don't just take our word for it - hear from our satisfied customers about their experiences with our services.</p>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Happy Pets</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfaction Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">1K+</div>
                            <div class="stat-label">5-Star Reviews</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">5</div>
                            <div class="stat-label">Years of Excellence</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Testimonials Grid -->
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-quote">"</div>
                        <h3 class="testimonial-name"><?php echo htmlspecialchars($testimonial['name']); ?></h3>
                        <p class="testimonial-subject"><?php echo htmlspecialchars($testimonial['subject']); ?></p>
                        <p class="testimonial-date"><?php echo date('F j, Y', strtotime($testimonial['created_at'])); ?></p>
                    </div>
                    <div class="testimonial-body">
                        <div class="testimonial-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['message']); ?></p>
                        <span class="testimonial-pet-type">
                            <i class="fas fa-paw"></i> 
                            <?php 
                            $petTypes = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster'];
                            echo $petTypes[array_rand($petTypes)];
                            ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Review Form -->
        <div class="review-form">
            <h3 class="form-title">Share Your Experience</h3>
            <form action="submit_testimonial.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Review Title</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Your Review</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <div class="d-flex gap-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rating" id="rating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i == 5 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="rating<?php echo $i; ?>">
                                    <?php for ($j = 0; $j < $i; $j++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Experience Premium Pet Care?</h2>
            <p class="cta-text">Join thousands of satisfied pet owners who trust FurShield with their furry family members. Sign up today and discover the difference.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light">Get Started</a>
                <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'?>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

    // Testimonial card animation
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    testimonialCards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(50px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
</script>
</body>
</html>
<?php
// Close database connection if it exists
if (isset($conn)) {
    $conn->close();
}
?>