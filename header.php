<?php

$isOwner = isset($_SESSION['role']) && $_SESSION['role'] === 'owner';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FurShield - Premium Pet Adoption</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --royal-brown: #6d4c3d;
      --royal-brown-dark: #5a3c2e;
      --accent: #c89b7b;
      --accent-dark: #b08362;
      --dark: #2a2a2a;
      --light: #ffffff;
      --gray-light: #f5f5f5;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: #f8f4e9;
    }

    /* Navbar */
    .navbar {
      background: var(--royal-brown);
      color: var(--light);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 1.5rem;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: var(--shadow);
    }

    .navbar .logo {
      display: flex;
      align-items: center;
      font-size: 1.7rem;
      font-weight: 700;
      color: var(--light);
      text-decoration: none;
    }

    .logo-img {
      height: 50px;
      margin-right: 0.5rem;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      list-style: none;
      margin: 0;
      padding: 0;
      align-items: center;
    }

    .nav-links li {
      position: relative;
    }

    .nav-links a {
      color: var(--light);
      text-decoration: none;
      font-size: 1rem;
      font-weight: 500;
      transition: var(--transition);
    }

    .nav-links a:hover {
      color: var(--accent);
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background: var(--light);
      color: var(--dark);
      min-width: 200px;
      border-radius: 6px;
      box-shadow: var(--shadow);
      overflow: hidden;
      z-index: 999;
    }

    .dropdown a {
      display: block;
      padding: 0.8rem 1rem;
      color: var(--dark);
      text-decoration: none;
    }

    .dropdown a:hover {
      background: var(--gray-light);
      color: var(--royal-brown);
    }

    .nav-links li:hover .dropdown {
      display: block;
    }

    /* Buttons */
    .btn {
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 6px;
      background: var(--accent);
      color: var(--light);
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
    }

    .btn:hover {
      background: var(--accent-dark);
    }

    /* Profile menu */
    .profile-menu {
      position: relative;
      display: inline-block;
    }

    .profile-btn {
      display: flex;
      align-items: center;
      background: var(--royal-brown-dark);
      padding: 0.5rem 0.9rem;
      border-radius: 50px;
      color: var(--light);
      cursor: pointer;
    }

    .profile-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.6rem;
    }

    .profile-menu .dropdown {
      right: 0;
      left: auto;
    }

    .profile-menu.active .dropdown {
      display: block;
    }

    /* Hamburger for mobile */
    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
    }

    .hamburger span {
      width: 25px;
      height: 3px;
      background: var(--light);
      border-radius: 3px;
      transition: all 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 991px) {
      .nav-links {
        position: fixed;
        top: 0;
        right: -100%;
        height: 100vh;
        width: 250px;
        background: var(--royal-brown);
        flex-direction: column;
        padding: 3rem 1.5rem;
        gap: 1rem;
        transition: right 0.3s ease;
        z-index: 999;
      }

      .nav-links.active {
        right: 0;
      }

      .nav-links li:hover .dropdown {
        display: none; /* Disable hover dropdown on mobile */
      }

      .dropdown {
        position: relative;
        top: 0;
        left: 0;
        background: var(--royal-brown-dark);
        border-radius: 6px;
      }

      .dropdown a {
        color: var(--light);
      }

      .hamburger {
        display: flex;
      }

      .nav-auth {
        display: none; /* Hide auth buttons, include inside mobile menu if needed */
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <a href="index.php" class="logo">
      <img src="images/logo.png" alt="FurShield Logo" class="logo-img">
      FurShield
    </a>

    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li>
        <a href="#">Adopt <i class="fas fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="#">All Pets</a>
          <a href="owner_pet.php">By Owners</a>
          <a href="pets.php">By Shelter</a>
        </div>
      </li>
      <li>
        <a href="#">Services <i class="fas fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="vets.php">Vet List</a>
          <a href="shelters.php">Shelter List</a>
        </div>
      </li>
      <li>
        <a href="#">More <i class="fas fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="aboutus.php">About Us</a>
          <a href="testimonials.php">Testimonials</a>
          <a href="contact.php">Contact Us</a>
        </div>
      </li>
      <?php if ($isOwner): ?>
      <li>
        <a href="#">Dashboard <i class="fas fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="my_pets.php">My Pets</a>
          <a href="add_pet.php">Add New Pet</a>
          <a href="book_appointment.php">Book Appointment</a>
          <a href="my_appointments.php">My Appointments</a>
          <a href="view_health.php">Health Records</a>
        </div>
      </li>
      <?php endif; ?>
    </ul>

    <div class="hamburger" id="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <div class="nav-auth">
      <a href="donate.php" class="btn" style="background:#e74c3c;">Donate</a>
      <?php if ($isOwner): ?>
        <div class="profile-menu" id="profileMenu">
          <div class="profile-btn">
            <div class="profile-avatar"><i class="fas fa-user-shield"></i></div>
            <span><?php echo htmlspecialchars($username); ?></span>
            <i class="fas fa-chevron-down"></i>
          </div>
          <div class="dropdown">
            <a href="profile.php">Profile</a>
            <a href="owner_request.php">Adoption Requests</a>
            <a href="cart.php">carts</a>
            <a href="logout.php" style="color:#e74c3c;">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn">Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <script>
    // Hamburger toggle
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('active');
    });

    // Profile menu toggle
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) {
      profileMenu.querySelector('.profile-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
      });
      document.addEventListener('click', function(e) {
        if (!profileMenu.contains(e.target)) {
          profileMenu.classList.remove('active');
        }
      });
    }
  </script>
</body>
</html>
