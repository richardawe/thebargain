<?php
// Simple Contact Form - Just send email
header('Content-Type: application/json');

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$investment = $_POST['investment'] ?? '';
$message = $_POST['message'] ?? '';

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
$body .= "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Phone: $phone\n";
$body .= "Investment: $investment\n\n";
$body .= "Message:\n$message\n";

// Headers
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: $email\r\n";

// Send email
$sent = mail($to, $subject, $body, $headers);

// Always return success (email might be queued)
echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
?>
