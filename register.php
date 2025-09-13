<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'owner';

    $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "✅ User registered successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Error: " . $stmt->error]);
    }
    exit; 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FurShield</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Poppins', sans-serif;
        }

        body {
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Video background with overlay */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .video-background video {
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 244, 233, 0.59);
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        header {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            padding: 1.2rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: var(--royal-brown);
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: var(--accent);
            font-weight: 800;
        }

        .logo i {
            margin-right: 12px;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .back-home {
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(109, 76, 61, 0.3);
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 0;
        }

        .form-container {
            background-color: var(--light);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            margin: 2rem;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(200, 155, 123, 0.2);
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(109, 76, 61, 0.2);
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, var(--accent), var(--royal-brown));
            border-radius: 10px 10px 0 0;
        }

        h2 {
            color: var(--royal-brown);
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
            font-weight: 600;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--accent), var(--royal-brown));
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 0.6rem;
            color: var(--medium-brown);
            font-weight: 500;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        label i {
            margin-right: 10px;
            color: var(--royal-brown);
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--light-accent);
            border-radius: 12px;
            font-size: 1rem;
            color: var(--dark);
            transition: var(--transition);
            background-color: var(--cream);
            font-weight: 400;
        }

        input::placeholder {
            color: #a99e95;
            font-weight: 400;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(200, 155, 123, 0.2);
            background-color: #fff;
        }

        input.error, select.error {
            border-color: #e74c3c;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: none;
            font-weight: 500;
        }

        button {
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            border: none;
            border-radius: 12px;
            padding: 1.2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(109, 76, 61, 0.3);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--medium-brown);
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--royal-brown);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--accent);
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .message {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
            display: none;
            animation: fadeIn 0.5s ease;
            font-weight: 500;
            border: 2px solid transparent;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success {
            background-color: #f0fff4;
            color: #2d8045;
            border-color: #c6f6d5;
        }

        .error-msg {
            background-color: #fff5f5;
            color: #c53030;
            border-color: #fed7d7;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 45px;
            cursor: pointer;
            color: var(--medium-brown);
            background: var(--cream);
            padding: 5px;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--royal-brown);
            background: var(--light-accent);
        }

        footer {
            background: linear-gradient(to right, var(--royal-brown), var(--medium-brown));
            color: var(--light);
            padding: 2.5rem 0;
            text-align: center;
            margin-top: auto;
            position: relative;
            z-index: 1;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer-content p {
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-links a {
            color: var(--light-accent);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a:hover {
            color: var(--light);
            transform: translateY(-2px);
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 15px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
            
            input, select {
                padding: 0.9rem 1.1rem;
            }
            
            button {
                padding: 1.1rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }

        .role-description {
            font-size: 0.8rem;
            color: var(--medium-brown);
            margin-top: 0.4rem;
            padding-left: 30px;
            display: none;
        }

        #role:focus ~ .role-description,
        .form-group:hover .role-description {
            display: block;
        }
        
        .illustration {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .illustration i {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="https://www.pexels.com/download/video/3939111/" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="video-overlay"></div>

    <header>
        <div class="container header-content">
            <a href="#" class="logo">
                <i class="fas fa-shield-dog"></i>
                Fur<span>Shield</span>
            </a>
            <a href="index.php" class="back-home">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>
    </header>

    <main>
        <div class="form-container">
            <div class="illustration">
                <i class="fas fa-paw"></i>
            </div>
            <h2>Create Your Account</h2>
            
            <div id="message" class="message"></div>
            
            <form id="signupForm" method="post" novalidate>
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    <div class="error-message" id="name-error">Please enter your full name</div>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    <div class="error-message" id="email-error">Please enter a valid email address</div>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a secure password" required>
                    <i class="password-toggle fas fa-eye" id="passwordToggle"></i>
                    <div class="error-message" id="password-error">Password must be at least 8 characters long</div>
                </div>
                
                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Account Type</label>
                    <select id="role" name="role" required>
                        <option value="owner">Pet Owner</option>
                        <option value="vet">Veterinarian</option>
                        <option value="shelter">Shelter</option>
                        <option value="visitor">Visitor</option>
                    </select>
                    <div class="role-description">Select the role that best describes you</div>
                    <div class="error-message" id="role-error">Please select an account type</div>
                </div>
                
                <button type="submit">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container footer-content">
            <p>&copy; 2023 FurShield. All rights reserved.</p>
            <div class="footer-links">
                <a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
                <a href="#"><i class="fas fa-file-contract"></i> Terms of Service</a>
                <a href="#"><i class="fas fa-phone"></i> Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signupForm');
            const messageDiv = document.getElementById('message');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            
            // Toggle password visibility
            passwordToggle.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordToggle.classList.remove('fa-eye');
                    passwordToggle.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordToggle.classList.remove('fa-eye-slash');
                    passwordToggle.classList.add('fa-eye');
                }
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset previous error states
                const errorElements = form.querySelectorAll('.error, .error-message');
                errorElements.forEach(el => {
                    el.classList.remove('error');
                    el.style.display = 'none';
                });
                
                // Validate inputs
                let isValid = true;
                
                // Name validation
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    showError(nameInput, 'name-error');
                    isValid = false;
                }
                
                // Email validation
                const emailInput = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    showError(emailInput, 'email-error');
                    isValid = false;
                }
                
                // Password validation
                if (passwordInput.value.length < 8) {
                    showError(passwordInput, 'password-error');
                    isValid = false;
                }
                
                if (isValid) {
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    submitBtn.disabled = true;
                    
                    // Submit the form via AJAX
                    const formData = new FormData(form);
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            showMessage(data.message, 'success');
                            form.reset();
                        } else {
                            showMessage(data.message, 'error-msg');
                        }
                    })
                    .catch(error => {
                        showMessage('An error occurred. Please try again.', 'error-msg');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                }
            });
            
            function showError(inputElement, errorElementId) {
                inputElement.classList.add('error');
                const errorElement = document.getElementById(errorElementId);
                errorElement.style.display = 'block';
            }
            
            function showMessage(text, className) {
                messageDiv.textContent = text;
                messageDiv.className = 'message ' + className;
                messageDiv.style.display = 'block';
                
                // Scroll to message
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Auto hide success message after 5 seconds
                if (className === 'success') {
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                       window.location.href = 'login.php'
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>