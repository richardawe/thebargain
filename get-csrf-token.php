<?php
/**
 * Generate and return CSRF token
 */

// Configure session for better cookie handling
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

session_start();

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
