<?php
/**
 * Simple Contact Form Handler
 * Just sends email - nothing else
 */

// Get form data
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$investment = isset($_POST['investment']) ? $_POST['investment'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Email configuration
$to = 'richard@thebargain.com.ng';
$subject = 'New Investment Inquiry - The Bargain';

// Email body
$body = "New investment inquiry received:\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Phone: $phone\n";
$body .= "Investment Interest: $investment\n\n";
$body .= "Message:\n$message\n";

// Email headers
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
$sent = @mail($to, $subject, $body, $headers);

// Return JSON response
header('Content-Type: application/json');
if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent. We will contact you soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again.']);
}
?>
