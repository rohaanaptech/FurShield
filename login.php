<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] == 'owner') {
                header("Location: index.php");
                exit();
            } elseif ($user['role'] == 'vet') {
                header("Location: vet/dashboard.php");
                exit();
            } elseif ($user['role'] == 'shelter') {
                header("Location: shelter/dashboard.php");
                exit();
            } elseif ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($user['role'] == 'visitor') {
                header("Location: index.php");
                exit();
            } else {
                $error_message = "❌ Unknown role!";
            }
        } else {
            $error_message = "❌ Invalid password!";
        }
    } else {
        $error_message = "❌ Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FurShield</title>
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
            background: rgba(248, 244, 233, 0.58);
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

        input {
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

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(200, 155, 123, 0.2);
            background-color: #fff;
        }

        input.error {
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

        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--medium-brown);
            font-size: 0.95rem;
        }

        .register-link a {
            color: var(--royal-brown);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
        }

        .register-link a:hover {
            color: var(--accent);
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .message {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
            display: <?php echo isset($error_message) ? 'block' : 'none'; ?>;
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
            
            input {
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
            <source src="https://www.pexels.com/download/video/3045714/" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="video-overlay"></div>

    <header>
        <div class="container header-content">
            <a href="index.php" class="logo">
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
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <h2>Welcome Back</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="message error-msg"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="post" novalidate>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="password-toggle fas fa-eye" id="passwordToggle"></i>
                </div>
                
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </form>
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
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
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            
            // Toggle password visibility
            if (passwordToggle && passwordInput) {
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
            }
        });
    </script>
</body>
</html>