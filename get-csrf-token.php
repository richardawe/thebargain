<?php
/**
 * Generate and return CSRF token
 */

session_start();

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
