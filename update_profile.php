<?php
session_start();
include '../config.php'; // mysqli connection ($conn)

// ----------------------
// Session & Access Check
// ----------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'shelter') {
    header("Location: ../login.php");
    exit();
}

$shelter_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// ----------------------
// Process form submission
// ----------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and update users table data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (empty($name)) {
        $errors[] = "Shelter name is required.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    
    // If no errors, proceed with updates
    if (empty($errors)) {
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $shelter_id);
        
        if ($stmt->execute()) {
            $_SESSION['name'] = $name; // Update session name
            
            // Handle shelter_profiles data
            $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
            $address = !empty($_POST['address']) ? trim($_POST['address']) : null;
            $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
            $established_year = !empty($_POST['established_year']) ? (int)$_POST['established_year'] : null;
            $shelter_type = !empty($_POST['shelter_type']) ? $_POST['shelter_type'] : null;
            
            // Process working hours
            $working_hours = [];
            if (!empty($_POST['hours'])) {
                foreach ($_POST['hours'] as $day => $times) {
                    if (!empty($times['open']) || !empty($times['close'])) {
                        $working_hours[$day] = [
                            'open' => !empty($times['open']) ? $times['open'] : null,
                            'close' => !empty($times['close']) ? $times['close'] : null
                        ];
                    }
                }
            }
            $working_hours_json = !empty($working_hours) ? json_encode($working_hours) : null;
            
            // Process social media links
            $facebook = !empty($_POST['facebook']) ? trim($_POST['facebook']) : null;
            $instagram = !empty($_POST['instagram']) ? trim($_POST['instagram']) : null;
            $twitter = !empty($_POST['twitter']) ? trim($_POST['twitter']) : null;
            $website = !empty($_POST['website']) ? trim($_POST['website']) : null;
            
            // Handle file upload
            $logo_path = null;
            if (!empty($_FILES['shelter_logo']['name'])) {
                $upload_dir = "../uploads/shelters/";
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['shelter_logo']['name']);
                $target_file = $upload_dir . $file_name;
                $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is actual image
                $check = getimagesize($_FILES['shelter_logo']['tmp_name']);
                if ($check !== false) {
                    // Check file size (max 5MB)
                    if ($_FILES['shelter_logo']['size'] > 5000000) {
                        $errors[] = "Sorry, your file is too large. Maximum size is 5MB.";
                    } else {
                        // Allow certain file formats
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                        if (in_array($image_file_type, $allowed_types)) {
                            if (move_uploaded_file($_FILES['shelter_logo']['tmp_name'], $target_file)) {
                                $logo_path = "uploads/shelters/" . $file_name;
                            } else {
                                $errors[] = "Sorry, there was an error uploading your file.";
                            }
                        } else {
                            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                        }
                    }
                } else {
                    $errors[] = "File is not an image.";
                }
            }
            
            // Check if shelter profile already exists
            $check_stmt = $conn->prepare("SELECT id FROM shelter_profiles WHERE shelter_id = ?");
            $check_stmt->bind_param("i", $shelter_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Update existing profile
                if ($logo_path) {
                    $update_stmt = $conn->prepare("
                        UPDATE shelter_profiles SET 
                        phone = ?, address = ?, shelter_logo = ?, description = ?, 
                        established_year = ?, shelter_type = ?, working_hours = ?,
                        facebook = ?, instagram = ?, twitter = ?, website = ?
                        WHERE shelter_id = ?
                    ");
                    $update_stmt->bind_param(
                        "ssssissssssi", 
                        $phone, $address, $logo_path, $description,
                        $established_year, $shelter_type, $working_hours_json,
                        $facebook, $instagram, $twitter, $website,
                        $shelter_id
                    );
                } else {
                    $update_stmt = $conn->prepare("
                        UPDATE shelter_profiles SET 
                        phone = ?, address = ?, description = ?, 
                        established_year = ?, shelter_type = ?, working_hours = ?,
                        facebook = ?, instagram = ?, twitter = ?, website = ?
                        WHERE shelter_id = ?
                    ");
                    $update_stmt->bind_param(
                        "sssissssssi", 
                        $phone, $address, $description,
                        $established_year, $shelter_type, $working_hours_json,
                        $facebook, $instagram, $twitter, $website,
                        $shelter_id
                    );
                }
            } else {
                // Insert new profile
             $update_stmt = $conn->prepare("
    INSERT INTO shelter_profiles 
    (shelter_id, shelter_logo, description, established_year, shelter_type, working_hours) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$update_stmt->bind_param(
    "isssss", 
    $shelter_id, $logo_path, $description, $established_year, $shelter_type, $working_hours_json
);

            }
            
            if (isset($update_stmt) && $update_stmt->execute()) {
                $success = true;
                $_SESSION['success_message'] = "Profile updated successfully!";
            } else {
                $errors[] = "Error updating shelter profile: " . $conn->error;
            }
            
        } else {
            $errors[] = "Error updating user information: " . $conn->error;
        }
    }
}

// Redirect back to profile page with messages
if ($success) {
    $_SESSION['success'] = true;
} else {
    $_SESSION['errors'] = $errors;
}

header("Location: profile.php");
exit();
?>