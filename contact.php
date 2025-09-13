<?php
session_start();
include 'config.php';

$createTableQuery = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTableQuery)) {
    error_log("Error creating table: " . $conn->error);
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "❌ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "❌ Please enter a valid email address.";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = "✅ Thank you for your message! We'll get back to you soon.";
            // Clear form fields
            $_POST = array();
        } else {
            $error_message = "❌ Sorry, there was an error sending your message. Please try again later.";
        }
        
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FurShield</title>
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

        /* Contact Section */
        .contact-hero {
            padding: 160px 0 80px;
            background: linear-gradient(to bottom, rgba(248, 244, 233, 0.9), rgba(248, 244, 233, 0.9)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23e7d8cc" opacity="0.2" x="0" y="0" width="100" height="100"/><path fill="%23c89b7b" opacity="0.2" d="M0 0L100 100M100 0L0 100" stroke-width="0.5"/></svg>');
            background-size: 300px;
            text-align: center;
        }

        .contact-title {
            font-size: 3.5rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .contact-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background-color: var(--accent);
            border-radius: 2px;
        }

        .contact-subtitle {
            font-size: 1.2rem;
            color: var(--medium-brown);
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        .contact-container {
            padding: 80px 0;
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
        }

        .contact-info {
            flex: 1;
            min-width: 300px;
        }

        .contact-form-container {
            flex: 2;
            min-width: 300px;
            background-color: var(--light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .contact-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, var(--accent), var(--royal-brown));
        }

        .form-title {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .form-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--accent);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--medium-brown);
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 10px;
            color: var(--royal-brown);
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
            min-height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.3);
        }

        .info-card {
            background-color: var(--light);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-left: 4px solid var(--accent);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(109, 76, 61, 0.1);
        }

        .info-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-title i {
            color: var(--accent);
            font-size: 1.5rem;
        }

        .info-content {
            color: var(--medium-brown);
            line-height: 1.7;
        }

        .info-content a {
            color: var(--royal-brown);
            text-decoration: none;
            transition: var(--transition);
        }

        .info-content a:hover {
            color: var(--accent);
        }

        .social-contact {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-contact a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-contact a:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(109, 76, 61, 0.3);
        }

        .message {
            padding: 15px;
            border-radius: 10px;
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

        /* Map Section */
        .map-container {
            padding: 0 0 80px;
        }

        .map-frame {
            height: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .map-frame iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        
        /* Responsive Design */
        @media (max-width: 992px) {
            .contact-container {
                flex-direction: column;
            }
            
            .contact-title {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .contact-title {
                font-size: 2.5rem;
            }
            
            .contact-form-container {
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<?php include 'header.php'?>

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="container">
        <h1 class="contact-title">Get In Touch</h1>
        <p class="contact-subtitle">Have questions or concerns about your furry friends? We're here to help! Reach out to our team and we'll get back to you as soon as possible.</p>
    </div>
</section>

<!-- Contact Content -->
<section class="contact-container">
    <div class="container">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-map-marker-alt"></i> Our Location</h3>
                    <p class="info-content">123 Pet Care Avenue<br>Animal City, AC 12345</p>
                </div>
                
                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-phone"></i> Call Us</h3>
                    <p class="info-content">
                        <a href="tel:+15551234567">(555) 123-4567</a><br>
                        <small>Monday-Friday, 9am-5pm</small>
                    </p>
                </div>
                
                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-envelope"></i> Email Us</h3>
                    <p class="info-content">
                        <a href="mailto:info@furshield.com">info@furshield.com</a><br>
                        <a href="mailto:support@furshield.com">support@furshield.com</a>
                    </p>
                </div>
                
                <div class="info-card">
                    <h3 class="info-title"><i class="fas fa-share-alt"></i> Follow Us</h3>
                    <div class="social-contact">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-container">
                    <h2 class="form-title">Send Us a Message</h2>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="message success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="message error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label class="form-label" for="name"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="subject"><i class="fas fa-tag"></i> Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="What is this regarding?" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message"><i class="fas fa-comment"></i> Your Message</label>
                            <textarea class="form-control" id="message" name="message" placeholder="Please describe your inquiry in detail..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="map-container">
    <div class="container">
        <div class="map-frame">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.621465758463!2d-74.0059493489482!3d40.71274937922721!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a1233dd7b6b%3A0x6a0936b8b2d492f4!2sPet%20Care%20Center!5e0!3m2!1sen!2sus!4v1645562345678!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- footer -->

<?php include 'footer.php'?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Header scroll effect
    const header = document.getElementById('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>
</body>
</html>