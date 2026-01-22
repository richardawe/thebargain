<?php
/**
 * Password Validation Endpoint for Investment Page
 * Server-side password validation for security
 */

// Configuration
$passwords_file = 'passwords.txt';
$session_timeout = 3600; // 1 hour

// Start session
session_start();

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting for password attempts
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = 'password_rate_limit.txt';
$max_attempts = 5;
$lockout_time = 900; // 15 minutes

// Check rate limiting
if (file_exists($rate_limit_file)) {
    $rate_data = json_decode(file_get_contents($rate_limit_file), true);
    if ($rate_data && isset($rate_data[$ip])) {
        $last_attempt = $rate_data[$ip]['time'];
        $attempts = $rate_data[$ip]['attempts'];
        
        if (time() - $last_attempt < $lockout_time) {
            if ($attempts >= $max_attempts) {
                http_response_code(429);
                echo json_encode([
                    'success' => false,
                    'message' => 'Too many failed attempts. Please try again in 15 minutes.'
                ]);
                exit;
            }
            $rate_data[$ip]['attempts']++;
        } else {
            // Reset after lockout period
            $rate_data[$ip] = ['time' => time(), 'attempts' => 1];
        }
    } else {
        $rate_data[$ip] = ['time' => time(), 'attempts' => 1];
    }
} else {
    $rate_data = [$ip => ['time' => time(), 'attempts' => 1]];
}

file_put_contents($rate_limit_file, json_encode($rate_data));

// Get and sanitize password
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// Load valid passwords
$valid_passwords = [];

// Try to load from file
if (file_exists($passwords_file)) {
    $file_content = file_get_contents($passwords_file);
    $lines = explode("\n", $file_content);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && substr($line, 0, 1) !== '#') {
            $valid_passwords[] = $line;
        }
    }
}

// Fallback to default passwords if file is empty
if (empty($valid_passwords)) {
    $valid_passwords = [
        'thebargain2024',
        'partner2024',
        'invest2024',
        'thebargain',
        'partner',
        'investment',
        'TB2024',
        'PARTNER2024'
    ];
}

// Validate password
if (in_array($password, $valid_passwords, true)) {
    // Password correct - set session
    $_SESSION['investment_authorized'] = true;
    $_SESSION['investment_auth_time'] = time();
    
    // Reset rate limiting on success
    if (isset($rate_data[$ip])) {
        unset($rate_data[$ip]);
        file_put_contents($rate_limit_file, json_encode($rate_data));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Access granted'
    ]);
} else {
    // Password incorrect
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Incorrect password. Please try again.'
    ]);
}
