<?php
require_once('config.php');

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $petId = intval($_GET['id']);
    
      
    $sql = "SELECT p.*, u.name as owner_name, u.email as owner_email 
            FROM pets p 
            LEFT JOIN users u ON p.owner_id = u.id 
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $petId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
        echo json_encode(['success' => true, 'pet' => $pet]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pet not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No pet ID provided']);
}
?>