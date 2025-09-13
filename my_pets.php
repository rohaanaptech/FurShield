<?php
session_start();
include 'config.php';
include 'auth.php';
requireOwner();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$ownerId = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM adoptable_pets WHERE owner_id=$ownerId");
$pets_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets - FurShield</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: var(--cream);
            color: var(--royal-brown);
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--royal-brown);
        }

        
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: var(--cream);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-accent);
        }

        .page-title {
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -17px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .add-pet-btn {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, var(--royal-brown), var(--accent));
            color: var(--light);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .add-pet-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(109, 76, 61, 0.3);
            color: var(--light);
        }

        .add-pet-btn i {
            margin-right: 8px;
        }

        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .pet-card {
            background: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(109, 76, 61, 0.1);
        }

        .pet-image {
            height: 200px;
            background: linear-gradient(135deg, var(--light-accent), var(--cream));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }

        .pet-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pet-details {
            padding: 25px;
        }

        .pet-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--royal-brown);
        }

        .pet-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .pet-info-item {
            display: flex;
            align-items: center;
            color: var(--medium-brown);
            font-size: 0.9rem;
        }

        .pet-info-item i {
            margin-right: 5px;
            color: var(--accent);
        }

        .pet-actions {
            display: flex;
            gap: 12px;
        }

        .pet-btn {
            flex: 1;
            padding: 10px 15px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .pet-btn-primary {
            background: var(--royal-brown);
            color: var(--light);
        }

        .pet-btn-primary:hover {
            background: var(--accent);
            color: var(--light);
        }

        .pet-btn-outline {
            border: 1px solid var(--royal-brown);
            color: var(--royal-brown);
        }

        .pet-btn-outline:hover {
            background: var(--royal-brown);
            color: var(--light);
        }

        .pet-btn-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .pet-btn-danger:hover {
            background: #dc3545;
            color: var(--light);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--light);
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--light-accent);
            margin-bottom: 20px;
        }

        .empty-text {
            color: var(--medium-brown);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .nav-items {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .nav-item {
                margin-bottom: 0;
            }
            
            .nav-link {
                padding: 10px 15px;
                border-radius: 8px;
            }
            
            .nav-link::before {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .pets-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                display: none;
            }
            
            .pet-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- header -->
     <?php include 'header.php'?>

        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">My Pets</h1>
                <a href="add_pet.php" class="add-pet-btn">
                    <i class="fas fa-plus-circle"></i> Add New Pet
                </a>
            </div>
<?php if ($pets_count > 0): ?>
    <div class="pets-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="pet-card">
                <div class="pet-image">
                    <?php if (!empty($row['photo'])): ?>
                        <img src="../<?php echo $row['photo']; ?>" alt="<?php echo htmlspecialchars($row['pet_name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-<?php echo strtolower($row['species']) === 'cat' ? 'cat' : 'dog'; ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="pet-details">
                    <h3 class="pet-name"><?php echo htmlspecialchars($row['pet_name']); ?></h3>
                    <div class="pet-info">
                        <span class="pet-info-item">
                            <i class="fas fa-paw"></i> <?php echo htmlspecialchars($row['species']); ?>
                        </span>
                        <span class="pet-info-item">
                            <i class="fas fa-dna"></i> <?php echo !empty($row['breed']) ? htmlspecialchars($row['breed']) : 'Unknown breed'; ?>
                        </span>
                        <span class="pet-info-item">
                            <i class="fas fa-birthday-cake"></i> <?php echo !empty($row['age']) ? htmlspecialchars($row['age']) . ' yrs' : 'Age unknown'; ?>
                        </span>
                        <span class="pet-info-item">
                            <i class="fas fa-venus-mars"></i> <?php echo !empty($row['gender']) ? ucfirst($row['gender']) : 'Unknown'; ?>
                        </span>
                        <span class="pet-info-item">
                            <i class="fas fa-ruler-combined"></i> <?php echo !empty($row['size']) ? ucfirst($row['size']) : 'Not specified'; ?>
                        </span>
                        <span class="pet-info-item">
                            <i class="fas fa-check-circle"></i> Status: <?php echo $row['status']; ?>
                        </span>
                    </div>
                    <p class="pet-description">
                        <?php echo !empty($row['description']) ? htmlspecialchars($row['description']) : 'No description available.'; ?>
                    </p>
                    <div class="pet-actions">
                        <a href="edit_pet.php?id=<?php echo $row['id']; ?>" class="pet-btn pet-btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete_pet.php?id=<?php echo $row['id']; ?>" 
                           class="pet-btn pet-btn-danger" 
                           onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($row['pet_name']); ?>?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p class="no-pets">You haven’t added any pets yet. Start by adding your first pet!</p>


                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <h3>No Pets Yet</h3>
                    <p class="empty-text">You haven't added any pets to your profile yet. Get started by adding your first furry friend!</p>
                    <a href="add_pet.php" class="add-pet-btn">
                        <i class="fas fa-plus-circle"></i> Add Your First Pet
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- footer -->
<?php include 'footer.php'?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>