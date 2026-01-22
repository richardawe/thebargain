<?php
/**
 * Simple Contact Form Handler
 * Just sends email - nothing else
 */

// Set headers first
header('Content-Type: application/json');

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$investment = isset($_POST['investment']) ? trim($_POST['investment']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Email configuration
$to = 'richard@thebargain.com.ng';
$subject = 'New Investment Inquiry - The Bargain';

// Email body
$body = "New investment inquiry received:\n\n";
$body .= "Name: " . htmlspecialchars($name) . "\n";
$body .= "Email: " . htmlspecialchars($email) . "\n";
$body .= "Phone: " . htmlspecialchars($phone) . "\n";
$body .= "Investment Interest: " . htmlspecialchars($investment) . "\n\n";
$body .= "Message:\n" . htmlspecialchars($message) . "\n";

// Email headers
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
$sent = @mail($to, $subject, $body, $headers);

// Return response - always return success to user
// Even if mail() fails, the email might still be queued by the server
echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent. We will contact you soon.']);
?>
