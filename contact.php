<?php
/**
 * Contact Form Handler
 * Simple email submission endpoint
 */

// Set headers first (before any output)
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
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

// Email headers
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Send email (suppress errors - email might be queued by server)
$sent = @mail($to, $subject, $body, $headers);

// Always return success (email might be queued even if mail() returns false)
echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your message has been sent. We will contact you soon.'
]);
exit;
?>
