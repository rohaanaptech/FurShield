<?php
session_start();
include 'config.php';

include 'auth.php';



// ✅ Fetch only pending adoption requests for this owner’s pets
$sql = "SELECT * FROM adoptable_pets WHERE owner_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Adoption Requests</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .card-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pet-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #007bff;
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        .card-content {
            padding: 15px;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .card-content p {
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>📋 Adoption Requests for Your Pets</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="pets-grid">
            <?php while ($pet = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-image">
                        <img src="<?= !empty($pet['photo']) ? htmlspecialchars($pet['photo']) : 'placeholder.jpg' ?>" 
                             alt="<?= htmlspecialchars($pet['pet_name']) ?>">
                        <div class="pet-badge">Owner Pet</div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($pet['pet_name']) ?></h3>
                        <p>Breed: <?= htmlspecialchars($pet['breed'] ?? 'Unknown') ?></p>
                        <p>Age: <?= htmlspecialchars($pet['age']) ?> yrs</p>
                        <p>Status: <?= htmlspecialchars($pet['status']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No pending adoption requests for your pets.</p>
    <?php endif; ?>

</body>
</html>
