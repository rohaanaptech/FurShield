<?php
include 'config.php';

$pet_id = $_GET['pet_id'] ?? null;
$type   = $_GET['type'] ?? null;

if(!$pet_id || !$type) {
    die("❌ Invalid request!");
}

if ($type == 'owner') {
    // adoptable_pets table
    $sql = "SELECT * FROM adoptable_pets WHERE id='$pet_id' AND status='Available'";
    $res = mysqli_query($conn, $sql);
    $pet = mysqli_fetch_assoc($res);

    if(!$pet) die("❌ Pet not found or already adopted!");

    // Update status
    mysqli_query($conn, "UPDATE adoptable_pets SET status='Pending' WHERE id='$pet_id'");

    echo "<h2>✅ Adoption request submitted!</h2>";
    echo "<p>You requested to adopt <strong>".$pet['pet_name']."</strong> (Owner's Pet).</p>";
    echo "<a href='owner_pets.php'>⬅ Back to Owner Pets</a>";

} elseif ($type == 'shelter') {
    // pets table
    $sql = "SELECT * FROM pets WHERE id='$pet_id' AND status='available'";
    $res = mysqli_query($conn, $sql);
    $pet = mysqli_fetch_assoc($res);

    if(!$pet) die("❌ Pet not found or already adopted!");

    // Update status
    mysqli_query($conn, "UPDATE pets SET status='pending' WHERE id='$pet_id'");

    echo "<h2>✅ Adoption request submitted!</h2>";
    echo "<p>You requested to adopt <strong>".$pet['name']."</strong> (Shelter's Pet).</p>";
    echo "<a href='shelter/shelter_pets.php'>⬅ Back to Shelter Pets</a>";

} else {
    echo "❌ Invalid type!";
}
?>
