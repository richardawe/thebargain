<?php
/**
 * Contact Form Handler for The Bargain
 * Handles form submissions, sends emails, and stores submissions
 */

// Configuration
$admin_email = 'richard@thebargain.com.ng';
$site_name = 'The Bargain';
$submissions_file = 'submissions.txt';

// Set headers to prevent caching
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and sanitize form data
$name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8')) : '';
$investment = isset($_POST['investment']) ? trim(htmlspecialchars($_POST['investment'], ENT_QUOTES, 'UTF-8')) : '';
$message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8')) : '';

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name is required and must be at least 2 characters';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($phone) || strlen($phone) < 10) {
    $errors[] = 'Valid phone number is required';
}

if (empty($investment)) {
    $errors[] = 'Investment interest is required';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message is required and must be at least 10 characters';
}

// Basic spam protection - check for common spam keywords
$spam_keywords = ['viagra', 'casino', 'poker', 'loan', 'credit', 'debt'];
$message_lower = strtolower($message);
foreach ($spam_keywords as $keyword) {
    if (strpos($message_lower, $keyword) !== false) {
        $errors[] = 'Message contains inappropriate content';
        break;
    }
}

// Rate limiting - prevent too many submissions from same IP
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = 'rate_limit.txt';
$rate_limit_time = 300; // 5 minutes
$rate_limit_count = 3; // Max 3 submissions per 5 minutes

if (file_exists($rate_limit_file)) {
    $rate_limit_data = json_decode(file_get_contents($rate_limit_file), true);
    if ($rate_limit_data && isset($rate_limit_data[$ip])) {
        $last_submission = $rate_limit_data[$ip]['time'];
        $submission_count = $rate_limit_data[$ip]['count'];
        
        if (time() - $last_submission < $rate_limit_time) {
            if ($submission_count >= $rate_limit_count) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Too many submissions. Please try again later.']);
                exit;
            }
            $rate_limit_data[$ip]['count']++;
        } else {
            $rate_limit_data[$ip] = ['time' => time(), 'count' => 1];
        }
    } else {
        $rate_limit_data[$ip] = ['time' => time(), 'count' => 1];
    }
} else {
    $rate_limit_data = [$ip => ['time' => time(), 'count' => 1]];
}

file_put_contents($rate_limit_file, json_encode($rate_limit_data));

// If there are errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

// Prepare email content
$investment_labels = [
    'minimum' => 'Minimum Investment (₦500,000+)',
    'associate' => 'Associate Producer (₦1M-₦1.9M)',
    'executive' => 'Executive Producer (₦2M+)',
    'other' => 'Other Amount'
];

$investment_label = isset($investment_labels[$investment]) ? $investment_labels[$investment] : $investment;

// Email to admin
$admin_subject = "New Investment Inquiry from {$site_name}";
$admin_body = "You have received a new investment inquiry from {$site_name} website.\n\n";
$admin_body .= "Contact Details:\n";
$admin_body .= "Name: {$name}\n";
$admin_body .= "Email: {$email}\n";
$admin_body .= "Phone: {$phone}\n";
$admin_body .= "Investment Interest: {$investment_label}\n\n";
$admin_body .= "Message:\n{$message}\n\n";
$admin_body .= "---\n";
$admin_body .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";
$admin_body .= "IP Address: {$ip}\n";

// Email to user (confirmation)
$user_subject = "Thank you for your interest in {$site_name}";
$user_body = "Dear {$name},\n\n";
$user_body .= "Thank you for your interest in investing in {$site_name}.\n\n";
$user_body .= "We have received your inquiry and will get back to you shortly.\n\n";
$user_body .= "Your inquiry details:\n";
$user_body .= "Investment Interest: {$investment_label}\n\n";
$user_body .= "If you have any urgent questions, please contact us at:\n";
$user_body .= "Email: {$admin_email}\n";
$user_body .= "Phone: +447927292051\n\n";
$user_body .= "Best regards,\n";
$user_body .= "The {$site_name} Team\n";

// Email headers
$headers = "From: {$site_name} <noreply@thebargain.com.ng>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$user_headers = "From: {$site_name} <{$admin_email}>\r\n";
$user_headers .= "Reply-To: {$admin_email}\r\n";
$user_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$user_headers .= "MIME-Version: 1.0\r\n";
$user_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send emails
$admin_email_sent = @mail($admin_email, $admin_subject, $admin_body, $headers);
$user_email_sent = @mail($email, $user_subject, $user_body, $user_headers);

// Store submission in file
$submission_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $ip,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'investment' => $investment_label,
    'message' => $message,
    'admin_email_sent' => $admin_email_sent,
    'user_email_sent' => $user_email_sent
];

// Append to submissions file
$submission_line = json_encode($submission_data) . "\n";
@file_put_contents($submissions_file, $submission_line, FILE_APPEND | LOCK_EX);

// Return response
if ($admin_email_sent || $user_email_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent. We will contact you soon.'
    ]);
} else {
    // Even if email fails, we stored the submission
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been received. We will contact you soon.'
    ]);
}
?>
