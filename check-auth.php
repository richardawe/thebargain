<?php
/**
 * Check if user is authorized to access investment page
 */

// Configure session for better cookie handling
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

session_start();

// Check if authorized
$authorized = false;
if (isset($_SESSION['investment_authorized']) && $_SESSION['investment_authorized'] === true) {
    $auth_time = $_SESSION['investment_auth_time'] ?? 0;
    $session_timeout = 3600; // 1 hour
    
    if (time() - $auth_time < $session_timeout) {
        $authorized = true;
    } else {
        // Session expired
        unset($_SESSION['investment_authorized']);
        unset($_SESSION['investment_auth_time']);
    }
}

header('Content-Type: application/json');
echo json_encode(['authorized' => $authorized]);
