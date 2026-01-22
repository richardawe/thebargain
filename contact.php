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

// Save submission to file (backup even if email fails)
$submissions_file = 'submissions.txt';
$submission_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'investment' => $investment,
    'message' => $message
];

// Format submission entry
$entry = "========================================\n";
$entry .= "Date: " . $submission_data['timestamp'] . "\n";
$entry .= "IP: " . $submission_data['ip'] . "\n";
$entry .= "Name: " . $submission_data['name'] . "\n";
$entry .= "Email: " . $submission_data['email'] . "\n";
$entry .= "Phone: " . $submission_data['phone'] . "\n";
$entry .= "Investment: " . $submission_data['investment'] . "\n";
$entry .= "Message: " . $submission_data['message'] . "\n";
$entry .= "========================================\n\n";

// Save to file with file locking for safety
try {
    $fp = fopen($submissions_file, 'a');
    if ($fp && flock($fp, LOCK_EX)) {
        fwrite($fp, $entry);
        flock($fp, LOCK_UN);
        fclose($fp);
    } else {
        // If file locking fails, try without lock (less safe but better than nothing)
        if ($fp) {
            fclose($fp);
        }
        // Fallback: append without locking
        file_put_contents($submissions_file, $entry, FILE_APPEND | LOCK_EX);
    }
} catch (Exception $e) {
    // Log error but don't fail the submission
    error_log("Contact form: Failed to save submission to file: " . $e->getMessage());
}

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
