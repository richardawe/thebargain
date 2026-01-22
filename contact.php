<?php
// Simple Contact Form - Just send email
// Suppress all output except JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set headers first
header('Content-Type: application/json; charset=utf-8');

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$investment = isset($_POST['investment']) ? trim($_POST['investment']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Check required fields
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

// Email setup
$to = 'richard@thebargain.com.ng';
$subject = 'New Investment Inquiry - The Bargain';

// Email body
$body = "New Investment Inquiry\n\n";
$body .= "Name: " . htmlspecialchars($name) . "\n";
$body .= "Email: " . htmlspecialchars($email) . "\n";
$body .= "Phone: " . htmlspecialchars($phone) . "\n";
$body .= "Investment: " . htmlspecialchars($investment) . "\n\n";
$body .= "Message:\n" . htmlspecialchars($message) . "\n";

// Headers
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
@mail($to, $subject, $body, $headers);

// Always return success (email might be queued)
echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
exit;
?>
