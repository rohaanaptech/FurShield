<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "furshield";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch adoptable pets
$pets_sql = "SELECT * FROM pets ";
$pets_result = $conn->query($pets_sql);
$isOwner = isset($_SESSION['role']) && $_SESSION['role'] === 'owner';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
// Fetch reviews
$reviews_sql = "SELECT * FROM reviews ";
$reviews_result = $conn->query($reviews_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurShield - Premium Pet Care & Adoption</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
   <style>
        :root {
            --cream: #f8f4e9;
            --royal-brown: #6d4c3d;
            --accent: #c89b7b;
            --dark: #2a2a2a;
            --light: #ffffff;
            --light-accent: #e7d8cc;
            --gold: #d4af37;
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--cream);
            color: var(--dark);
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.3;
        }

        /* Container for consistent alignment */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #e7d8cc, #8a7365); /* gradient like example */
  color: #fff;
  padding: 100px 0;
  position: relative;
}

.hero-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap; /* responsive */
  gap: 40px;
}

.hero-text {
  flex: 1 1 500px;
}

.hero-text h1 {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
}

.hero-text h1 span {
  color: #fff;
  font-weight: 800;
}

.hero-text p {
  margin: 20px 0;
  font-size: 1.1rem;
  color: #f0f0f0;
}

.hero-buttons {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.btn-primary,
.btn-secondary {
  display: inline-block;
  padding: 12px 28px;
  border-radius: 30px;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-primary {
  background: #fff;
  color: #8a7365;
}

.btn-primary:hover {
  background: #f0f0f0;
}

.btn-secondary {
  border: 2px solid #fff;
  color: #fff;
}

.btn-secondary:hover {
  background: rgba(255,255,255,0.2);
}

.hero-image {
  flex: 1 1 500px;   /* take more space */
  text-align: center;
}

.hero-image img {
  max-width: 120%;   /* bigger than container */
  height: auto;
  border-radius: 12px;
  transform: scale(1.1); /* slight zoom effect */
}
        .pet-card {
            position: absolute;
            width: 280px;
            background: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
            transform-style: preserve-3d;
        }

        .pet-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: var(--transition);
        }

        .pet-card-content {
            padding: 1.5rem;
        }

        .pet-card h3 {
            font-size: 1.2rem;
            color: var(--royal-brown);
            margin-bottom: 0.5rem;
        }

        .pet-card p {
            font-size: 0.9rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .pet-card-btn {
            padding: 0.5rem 1rem;
            background: var(--accent);
            color: var(--light);
            border: none;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .pet-card-btn:hover {
            background: var(--royal-brown);
        }

        /* Specific pet cards */
        .pet-card-1 {
            top: 0;
            left: 0;
            transform: rotate(-5deg);
            animation: float 6s ease-in-out infinite;
        }

        .pet-card-2 {
            top: 50px;
            right: 0;
            transform: rotate(3deg);
            animation: float 7s ease-in-out infinite 1s;
        }

        .pet-card-3 {
            bottom: 0;
            left: 50px;
            transform: rotate(-2deg);
            animation: float 8s ease-in-out infinite 0.5s;
        }

        .pet-card:hover {
            transform: scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .pet-card:hover img {
            transform: scale(1.1);
        }

        /* Background elements */
        .hero-bg-element {
            position: absolute;
            z-index: 1;
        }

        .bg-circle-1 {
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(200, 155, 123, 0.1);
            top: -250px;
            right: -250px;
            animation: pulse 8s ease-in-out infinite;
        }

        .bg-circle-2 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(109, 76, 61, 0.1);
            bottom: -150px;
            left: -150px;
            animation: pulse 10s ease-in-out infinite 1s;
        }

        .bg-paw {
            font-size: 200px;
            opacity: 0.03;
            color: var(--royal-brown);
            position: absolute;
            animation: rotate 20s linear infinite;
        }

        .bg-paw-1 {
            top: 20%;
            left: 10%;
        }

        .bg-paw-2 {
            bottom: 20%;
            right: 10%;
            animation-delay: -10s;
        }

        /* Premium Services Section */
        .services-section {
            padding: 5rem 2rem;
            background: var(--light);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--cream);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--light-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--royal-brown);
            transition: var(--transition);
        }

        .service-card:hover .service-icon {
            background: var(--royal-brown);
            color: var(--light);
            transform: scale(1.1);
        }

        .service-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--royal-brown);
        }

        .service-description {
            color: var(--dark);
            opacity: 0.8;
        }

        /* Premium Pets Grid */
        .section {
            padding: 5rem 2rem;
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 2.8rem;
            margin-bottom: 3rem;
            color: var(--royal-brown);
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent);
            margin: 0.5rem auto;
            border-radius: 2px;
        }

        .pets-grid, .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            position: relative;
        }

        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .card:hover .card-image img {
            transform: scale(1.1);
        }

        .pet-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            padding: 1.8rem;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
            color: var(--royal-brown);
        }

        .card-details {
            margin-bottom: 1.5rem;
        }

        .card-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--dark);
            opacity: 0.8;
        }

        .card-button {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            background: var(--royal-brown);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-align: center;
            width: 100%;
            border: none;
            cursor: pointer;
        }

        .card-button:hover {
            background: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.3);
        }

        /* Premium Reviews Section */
        .reviews-section {
            background: var(--light-accent);
            position: relative;
            overflow: hidden;
        }

        .reviews-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="%23c89b7b33" d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20c-4.42,0-8-3.58-8-8s3.58-8,8-8 s8,3.58,8,8S16.42,20,12,20z"/></svg>');
            background-size: contain;
            opacity: 0.3;
            animation: rotate 60s linear infinite;
        }

        .reviews-section::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="%236d4c3d22" d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20c-4.42,0-8-3.58-8-8s3.58-8,8-8 s8,3.58,8,8S16.42,20,12,20z"/></svg>');
            background-size: contain;
            opacity: 0.2;
            animation: rotate 80s linear infinite reverse;
        }

        .review-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .review-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .review-card::before {
            content: '"';
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 5rem;
            color: var(--accent);
            opacity: 0.15;
            font-family: 'Playfair Display', serif;
            line-height: 1;
        }

        .review-content {
            position: relative;
            z-index: 1;
        }

        .review-text {
            font-style: italic;
            margin-bottom: 1.5rem;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .review-author {
            font-weight: 700;
            color: var(--royal-brown);
            font-size: 1.1rem;
        }

        .review-subject {
            font-size: 0.9rem;
            color: var(--dark);
            opacity: 0.8;
        }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, var(--royal-brown) 0%, var(--accent) 100%);
            padding: 5rem 2rem;
            color: white;
            text-align: center;
        }

        .newsletter-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .newsletter-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .newsletter-text {
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            outline: none;
        }

        .newsletter-btn {
            padding: 1rem 2rem;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-btn:hover {
            background: var(--gold);
            transform: translateY(-3px);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(3deg);
            }
            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 0.5;
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .pet-card {
                width: 240px;
            }
        }

        @media (max-width: 992px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-images {
                height: 500px;
            }
            
            .pet-card {
                width: 220px;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .hero-stats {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .hero-stats {
                flex-wrap: wrap;
            }
            
            .section {
                padding: 3rem 1.5rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }

            .newsletter-form {
                flex-direction: column;
            }
            
            .bg-circle-1 {
                width: 300px;
                height: 300px;
                top: -150px;
                right: -150px;
            }
            
            .bg-circle-2 {
                width: 200px;
                height: 200px;
                bottom: -100px;
                left: -100px;
            }
            
            .bg-paw {
                font-size: 150px;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                justify-content: space-around;
            }
            
            .stat-item {
                flex: 0 0 45%;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .pets-grid, .reviews-grid, .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

  <!-- Premium Header -->
  <?php include 'header.php'; ?>

  <!-- Premium Hero Section -->
  <!-- <section class="hero">
    <div class="hero-content " style="margin-left: 90px;">
      <h1 class="hero-title">Find Your <span class="highlight">Perfect Furry</span> Companion</h1>
      <p class="hero-subtitle">At FurShield, we provide premium care and loving homes for pets in need. Experience the joy of adoption <br> with our exclusive matching service.</p>
      
      <div class="hero-buttons">
        <a href="#adoptable-pets" class="btn-hero primary"><i class="fas fa-paw"></i> Adopt Now</a>
        <a href="#" class="btn-hero outline"><i class="fas fa-play-circle"></i> How It Works</a>
      </div>
      
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-number">500+</div>
          <div class="stat-label">Pets Adopted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">98%</div>
          <div class="stat-label">Success Rate</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">24/7</div>
          <div class="stat-label">Support</div>
        </div>
      </div>
    </div>
  </section> -->
<section class="hero-section">
  <div class="container hero-content">
    <div class="hero-text">
 <h1 class="hero-title" style="color:var( --royal-brown);" >Find Your <span class="highlight">Perfect Furry</span> Companion</h1>
      <p class="hero-subtitle">At FurShield, we provide premium care and loving homes for pets in need. Experience the joy of adoption <br> with our exclusive matching service.</p>
      <div class="hero-buttons">
        <a href="aboutus.php" class="btn-primary">About Us</a>
        <a href="owner_pets.php" class="btn-secondary">Meet our Pets</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="images/bgbgbg.png"  alt="Pet Image">
    </div>
  </div>
</section>
  <!-- Premium Services Section -->
  <section class="services-section">
    <h2 class="section-title">Our Premium Services</h2>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-stethoscope"></i>
        </div>
        <h3 class="service-title">Veterinary Care</h3>
        <p class="service-description">Comprehensive health services provided by certified veterinarians to keep your pets healthy and happy.</p>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-spa"></i>
        </div>
        <h3 class="service-title">Grooming</h3>
        <p class="service-description">Premium grooming services to keep your pets looking their best and feeling comfortable.</p>
      </div>
      
      <div class="service-card">
        <div class="service-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <h3 class="service-title">Training</h3>
        <p class="service-description">Professional training programs to help your pets develop good behavior and strengthen your bond.</p>
      </div>
    </div>
  </section>

  <!-- Adoptable Pets -->
  <section id="adoptable-pets" class="section">
    <h2 class="section-title">Adoptable Pets</h2>
    <div class="pets-grid">
      <?php if ($pets_result && $pets_result->num_rows > 0): ?>
        <?php while($pet = $pets_result->fetch_assoc()): ?>
          <div class="card">
           
      <div class="card-image">
    <?php 
        $photo = isset($pet['photo']) && !empty($pet['photo']) ? 'uploads/pets/' . $pet['photo'] : 'uploads/pets/placeholder.jpg';
        $petName = isset($pet['pet_name']) ? $pet['pet_name'] : 'Unknown Pet';
    ?>
    <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($petName); ?>" style="width:100%; height:200px; object-fit:cover;">
    <div class="pet-badge">Available</div>
</div>


            <div class="card-content">
              <h3 class="card-title"><?php echo $pet['name']; ?></h3>
              <div class="card-details">
                <div class="card-detail">
                  <i class="fas fa-paw"></i>
                  <span><?php echo ucfirst($pet['species']); ?></span>
                </div>
                <div class="card-detail">
                  <i class="fas fa-dna"></i>
                  <span><?php echo $pet['breed'] ?? 'Mixed Breed'; ?></span>
                </div>
                <div class="card-detail">
                  <i class="fas fa-birthday-cake"></i>
                  <span><?php echo $pet['age'] ?? 'Unknown'; ?> years old</span>
                </div>
              </div>
              <a href="adopt.php?id=<?php echo $pet['id']; ?>" class="card-button">Meet <?php echo $pet['name']; ?></a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; grid-column: 1 / -1;">No pets available for adoption right now. Please check back later!</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Reviews -->
  <section class="section reviews-section">
    <h2 class="section-title">What Pet Lovers Say</h2>
    <div class="reviews-grid">
      <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
        <?php while($review = $reviews_result->fetch_assoc()): ?>
          <div class="review-card">
            <div class="review-content">
              <p class="review-text"><?php echo $review['message']; ?></p>
              <div class="review-author"><?php echo $review['name']; ?></div>
              <div class="review-subject"><?php echo $review['subject']; ?></div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; grid-column: 1 / -1;">No reviews yet. Be the first to write one!</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Newsletter Section -->
  <section class="newsletter-section">
    <div class="newsletter-container">
      <h2 class="newsletter-title">Join Our Pet Community</h2>
      <p class="newsletter-text">Subscribe to our newsletter for updates on new pets, care tips, and exclusive offers.</p>
      <form class="newsletter-form">
        <input type="email" class="newsletter-input" placeholder="Your email address" required>
        <button type="submit" class="newsletter-btn">Subscribe</button>
      </form>
    </div>
  </section>

  <!-- footer -->

  <?php include 'footer.php'; ?>

  <script>
    // Header scroll effect
    window.addEventListener('scroll', function() {
      const header = document.getElementById('header');
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });

    // Animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.card, .review-card, .service-card');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = 1;
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold: 0.1 });
      
      cards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(50px)';
        card.style.transition = 'opacity 0.8s ease, transform 08.s ease';
        observer.observe(card);
      });
    });
  </script>

    <script>
    // Mobile menu functionality
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
      const navLinks = document.getElementById('navLinks');
      navLinks.classList.toggle('nav-active');
      
      // Change icon
      const icon = this.querySelector('i');
      if (navLinks.classList.contains('nav-active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });
    
    // Profile dropdown functionality
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
      const profileBtn = profileMenu.querySelector('.profile-btn');
      
      profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
      });
      
      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!profileMenu.contains(e.target)) {
          profileMenu.classList.remove('active');
        }
      });
    }
    
    // Mobile dropdown toggle
    if (window.innerWidth <= 768) {
      const navItems = document.querySelectorAll('.navbar ul li');
      navItems.forEach(item => {
        if (item.querySelector('.dropdown')) {
          item.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.nextElementSibling) {
              e.preventDefault();
              this.classList.toggle('active');
            }
          });
        }
      });
    }
    
    // Additional interactive animations for hero section
    document.addEventListener('DOMContentLoaded', function() {
      const petCards = document.querySelectorAll('.pet-card');
      
      // Add mousemove 3D tilt effect to pet cards
      petCards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
          const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
          const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
          this.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg) translateZ(50px)`;
        });
        
        card.addEventListener('mouseenter', function() {
          this.style.transition = 'none';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transition = 'transform 0.5s ease';
          this.style.transform = 'rotateY(0deg) rotateX(0deg) translateZ(30px)';
        });
      });
      
      // Animate stats counting up
      const statNumbers = document.querySelectorAll('.stat-number');
      const statsSection = document.querySelector('.hero-stats');
      
      if (statsSection) {
        const options = {
          threshold: 0.5
        };
        
        const observer = new IntersectionObserver(function(entries) {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              statNumbers.forEach(stat => {
                const target = +stat.innerText.replace('+', '');
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                  current += step;
                  if (current >= target) {
                    clearInterval(timer);
                    stat.innerText = stat.innerText.includes('+') ? target + '+' : target;
                  } else {
                    stat.innerText = Math.floor(current);
                  }
                }, 16);
              });
              
              observer.unobserve(statsSection);
            }
          });
        }, options);
        
        observer.observe(statsSection);
      }
    });
  </script>

</body>
</html>