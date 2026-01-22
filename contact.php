<?php
/**
 * Contact Form Handler
 * Simple email submission endpoint
 */

// Start output buffering to prevent any accidental output
ob_start();

// Disable error display but enable logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set headers first (before any output)
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$investment = isset($_POST['investment']) ? trim($_POST['investment']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Check required fields
if (empty($name) || empty($email) || empty($message)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Email setup
$to = 'richard@thebargain.com.ng';
$subject = 'New Investment Inquiry - The Bargain';

// Email body
$body = "New Investment Inquiry\n\n";
$body .= "Name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n";
$body .= "Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n";
$body .= "Phone: " . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "\n";
$body .= "Investment: " . htmlspecialchars($investment, ENT_QUOTES, 'UTF-8') . "\n\n";
$body .= "Message:\n" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "\n";

// Email headers - sanitize email to prevent header injection
$sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . $sanitized_email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Try to send email
$sent = false;
if (function_exists('mail')) {
    try {
        $sent = @mail($to, $subject, $body, $headers);
    } catch (Exception $e) {
        // Log the error but don't expose it to user
        error_log("Contact form mail error: " . $e->getMessage());
        $sent = false;
    }
} else {
    // Log that mail function is not available
    error_log("Contact form: mail() function is not available on this server");
}

// Clear any output buffer and return success
ob_end_clean();
echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your message has been sent. We will contact you soon.'
]);
exit;
