<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Sound Advice Hearing Solutions - Contact Form
|--------------------------------------------------------------------------
| File: send-mail.php
|
| IMPORTANT:
| 1. Upload this file to the SAME folder as contact.html.
| 2. Change $recipientEmail only if you want another receiving address.
| 3. The "From" address should normally use your own website domain.
| 4. Your hosting account must support PHP mail().
|--------------------------------------------------------------------------
*/

$recipientEmail = 'info@soundadvicehearing.ca';
$fromEmail      = 'info@soundadvicehearing.ca';
$clinicName     = 'Sound Advice Hearing Solutions';

/* Only accept POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

/* Simple honeypot spam protection */
if (!empty($_POST['website'] ?? '')) {
    header('Location: contact.html?status=success');
    exit;
}

/* Clean input */
function clean_input(string $value): string {
    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$name     = clean_input($_POST['name'] ?? '');
$phone    = clean_input($_POST['phone'] ?? '');
$email    = trim($_POST['email'] ?? '');
$location = clean_input($_POST['location'] ?? '');
$message  = trim($_POST['message'] ?? '');

/* Validate required fields */
if ($name === '' || $phone === '' || $email === '' || $location === '') {
    header('Location: contact.html?status=error');
    exit;
}

/* Validate email */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?status=error');
    exit;
}

/* Limit input sizes */
if (
    strlen($name) > 100 ||
    strlen($phone) > 50 ||
    strlen($email) > 150 ||
    strlen($location) > 80 ||
    strlen($message) > 5000
) {
    header('Location: contact.html?status=error');
    exit;
}

/* Build email */
$subject = 'New Hearing Assessment Request - ' . $name;

$body  = "A new hearing assessment request was submitted from the website.\n\n";
$body .= "----------------------------------------\n";
$body .= "PATIENT INFORMATION\n";
$body .= "----------------------------------------\n\n";
$body .= "Full Name: " . $name . "\n";
$body .= "Phone: " . $phone . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Preferred Location: " . $location . "\n\n";
$body .= "How Can We Help:\n";
$body .= ($message !== '' ? $message : 'No additional message provided.') . "\n\n";
$body .= "----------------------------------------\n";
$body .= "Submitted from: Sound Advice Hearing Solutions website\n";
$body .= "----------------------------------------\n";

/*
|--------------------------------------------------------------------------
| Email headers
|--------------------------------------------------------------------------
| Reply-To allows you to click "Reply" in your email and reply directly
| to the patient.
*/
$headers  = "From: " . $clinicName . " <" . $fromEmail . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

/* Send email */
$sent = mail(
    $recipientEmail,
    $subject,
    $body,
    $headers
);

/* Redirect back to contact page */
if ($sent) {
    header('Location: contact.html?status=success');
    exit;
}

header('Location: contact.html?status=error');
exit;
?>
