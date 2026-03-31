<?php
/**
 * Action: Contact Form Submission
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!csrf_validate()) {
    flash('danger', 'Security check failed. Please try again.');
    header('Location: /contact');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    flash('danger', 'All fields are required.');
    header('Location: /contact');
    exit;
}

// In a real app, this would send an email. For now, we'll log it to DB (messages table).
db_execute(
    "INSERT INTO messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)",
    [$name, $email, $subject, $message]
);

flash('success', 'Thank you! Your message has been sent. We will get back to you soon.');
header('Location: /contact');
exit;
