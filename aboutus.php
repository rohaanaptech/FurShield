<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FurShield Premium Pet Care</title>
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

        /* Header Styles */
        header {
            background: var(--cream);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(109, 76, 61, 0.08);
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

        .nav-link {
            color: var(--royal-brown);
            font-weight: 500;
            position: relative;
            padding: 5px 0;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--accent);
        }

        .btn {
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--royal-brown);
            color: var(--cream);
            border: 2px solid var(--royal-brown);
        }

        .btn-primary:hover {
            background-color: transparent;
            color: var(--royal-brown);
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

        /* Page Header */
        .page-header {
            padding: 150px 0 80px;
            background: linear-gradient(to right, rgba(248, 244, 233, 0.9), rgba(248, 244, 233, 0.7)), url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
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

        /* About Section */
        .about-section {
            padding: 80px 0;
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

        .about-content {
            margin-bottom: 80px;
        }

        .about-image {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(109, 76, 61, 0.15);
            transition: all 0.3s ease;
        }

        .about-image:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(109, 76, 61, 0.2);
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--medium-brown);
        }

        .highlight {
            color: var(--accent);
            font-weight: 600;
        }

        /* Mission & Vision */
        .mission-vision {
            background: linear-gradient(to right, var(--light), var(--light-accent));
            padding: 80px 0;
            border-radius: 30px;
            margin: 60px 0;
        }

        .mission-card, .vision-card {
            background: var(--light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.1);
            height: 100%;
            transition: all 0.3s ease;
        }

        .mission-card:hover, .vision-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.15);
        }

        .mission-icon, .vision-icon {
            width: 80px;
            height: 80px;
            background: var(--light-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: var(--royal-brown);
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

        /* Values Section */
        .values-section {
            padding: 80px 0;
        }

        .value-card {
            background: var(--light);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(109, 76, 61, 0.1);
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.15);
        }

        .value-icon {
            width: 70px;
            height: 70px;
            background: var(--light-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
            color: var(--royal-brown);
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

        /* Footer */
        footer {
            background: linear-gradient(to bottom, var(--royal-brown), #5a3e30);
            color: var(--cream);
            padding: 80px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--cream);
            display: flex;
            align-items: center;
        }

        .footer-logo i {
            margin-right: 10px;
            color: var(--accent);
        }

        .footer-about {
            opacity: 0.8;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .footer-heading {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--cream);
            position: relative;
            padding-bottom: 10px;
        }

        .footer-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--accent);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .footer-links li i {
            margin-right: 10px;
            color: var(--accent);
            width: 16px;
        }

        .footer-links a {
            color: var(--cream);
            opacity: 0.8;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            opacity: 1;
            padding-left: 5px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--cream);
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background-color: var(--accent);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.7;
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
            
            .about-image {
                margin-bottom: 30px;
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
        <h1 class="page-title">About FurShield</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">Our Story</h2>
        
        <div class="row about-content">
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="FurShield Team" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-text">
                    <p>Founded in 2018, <span class="highlight">FurShield</span> began with a simple mission: to create a world where every pet receives the love, care, and protection they deserve.</p>
                    
                    <p>What started as a small team of passionate veterinarians and pet lovers has grown into a comprehensive platform connecting pet owners with the best care resources available.</p>
                    
                    <p>Our name represents our commitment - <span class="highlight">every paw deserves a shield of love</span>. We believe that pets are family, and they deserve the same level of care and attention as any other family member.</p>
                    
                    <p>Today, FurShield serves thousands of pet owners across the country, providing access to veterinary care, premium products, and a supportive community of fellow pet enthusiasts.</p>
                </div>
            </div>
        </div>
        
        <!-- Mission & Vision -->
        <div class="mission-vision">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Our Mission</h3>
                        <p>To provide a comprehensive, accessible platform that empowers pet owners with the resources, knowledge, and community support needed to ensure their pets live healthy, happy lives.</p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="vision-card">
                        <div class="vision-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Our Vision</h3>
                        <p>To create a world where every pet receives the love and care they deserve, and where no pet owner feels alone in their journey to provide the best for their furry family members.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">5K+</div>
                            <div class="stat-label">Happy Pets</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">200+</div>
                            <div class="stat-label">Veterinarians</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Cities</div>
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
        
        <!-- Values Section -->
        <div class="values-section">
            <h2 class="section-title">Our Values</h2>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Compassion</h4>
                        <p>We approach every pet and owner with empathy, understanding, and genuine care.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Protection</h4>
                        <p>We prioritize the safety and well-being of every animal in our care.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Community</h4>
                        <p>We believe in the power of connection and support among pet lovers.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Education</h4>
                        <p>We empower pet owners with knowledge to make informed decisions.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Excellence</h4>
                        <p>We strive for the highest standards in everything we do.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h4>Trust</h4>
                        <p>We build relationships based on reliability, honesty, and integrity.</p>
                    </div>
                </div>
            </div>
        </div>
        
       

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Join Our Pet Loving Community</h2>
            <p class="cta-text">Become part of a community that puts pets first. Experience the FurShield difference in pet care and connect with fellow pet enthusiasts.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light">Create Account</a>
                <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
  <?php include 'footer.php'; ?>


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

    // Animation for elements
    const animateElements = document.querySelectorAll('.value-card, .team-member, .mission-card, .vision-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    animateElements.forEach(element => {
        element.style.opacity = 0;
        element.style.transform = 'translateY(50px)';
        element.style.transition = 'all 0.5s ease';
        observer.observe(element);
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