<?php
/*
 * Contact form handler for contact_us.html
 * Receives the form via POST (fetch) and sends it with PHPMailer over SMTP.
 * Responds with JSON: { "success": bool, "message": string }
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

function respond($success, $message, $status = 200)
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request.', 405);
}

$configFile = __DIR__ . '/mail_config.php';
if (!is_file($configFile)) {
    error_log('contact.php: mail_config.php is missing');
    respond(false, 'Sorry, the message could not be sent right now. Please try again later.', 500);
}
$config = require $configFile;

/* --- read input --- */
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$mobile  = trim($_POST['mobile'] ?? '');
$message = trim($_POST['message'] ?? '');

/* --- server-side validation (same rules as the page) --- */
$errors = [];
$mobileDigits = preg_replace('/[\s-]/', '', $mobile);

if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 60 || !preg_match("/^[A-Za-z\s.'-]+$/", $name)) {
    $errors[] = 'name';
}
if ($email === '' || mb_strlen($email) > 80 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
if (!preg_match('/^\+?\d{10,13}$/', $mobileDigits)) {
    $errors[] = 'mobile';
}
if ($message === '' || mb_strlen($message) < 10 || mb_strlen($message) > 1000) {
    $errors[] = 'message';
}

if ($errors) {
    respond(false, 'Please correct the highlighted fields and try again.', 422);
}

/* --- build and send the email --- */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_username'];
    $mail->Password   = $config['smtp_password'];
    $mail->Port       = (int) $config['smtp_port'];
    $mail->CharSet    = 'UTF-8';
    // fail fast instead of hanging until the proxy (e.g. Cloudflare 524) gives up
    $mail->Timeout    = (int) ($config['smtp_timeout'] ?? 15);

    $secure = $config['smtp_secure'] ?? 'ssl';
    if ($secure === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($secure === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        // no encryption: only for the mail server on the same machine (smtp_host 'localhost')
        $mail->SMTPSecure  = '';
        $mail->SMTPAutoTLS = false;
    }

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email'], $config['to_name']);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Website enquiry from ' . $name;

    $safe = function ($v) {
        return nl2br(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
    };
    $mail->Body =
        '<p><strong>Name:</strong> ' . $safe($name) . '</p>' .
        '<p><strong>Email:</strong> ' . $safe($email) . '</p>' .
        '<p><strong>Mobile:</strong> ' . $safe($mobile) . '</p>' .
        '<p><strong>Message:</strong><br>' . $safe($message) . '</p>';
    $mail->AltBody =
        "Name: $name\n" .
        "Email: $email\n" .
        "Mobile: $mobile\n\n" .
        "Message:\n$message";

    $mail->send();
    respond(true, 'Thank you, ' . $name . '. Your message has been sent - we will get back to you soon.');
} catch (Exception $e) {
    error_log('contact.php: mail error: ' . $mail->ErrorInfo);
    respond(false, 'Sorry, the message could not be sent right now. Please try again later.', 500);
}
