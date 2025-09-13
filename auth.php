<?php

// Default role = visitor if not logged in
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'visitor';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Helper function to protect owner-only pages
function requireOwner() {
    global $role;
    if ($role !== 'owner') {
        die("❌ Access denied! Only owners can access this page.");
    }
}
