<?php
/*
 * SMTP settings for contact.php
 *
 * Copy this file to mail_config.php (next to contact.php) on the server and
 * fill in real values. mail_config.php is ignored by git and is not part of
 * the cPanel deployment, so the password never ends up in the repository.
 */
return [
    // SMTP server (cPanel mail: usually mail.yourdomain.com)
    'smtp_host'     => 'mail.example.com',
    'smtp_port'     => 465,              // 465 = SSL, 587 = STARTTLS
    'smtp_secure'   => 'ssl',            // 'ssl' or 'tls'
    'smtp_username' => 'no-reply@example.com',
    'smtp_password' => 'CHANGE_ME',

    // Sender shown on the email (should be the SMTP account above)
    'from_email'    => 'no-reply@example.com',
    'from_name'     => 'PNT Tech Website',

    // Where contact form messages are delivered
    'to_email'      => 'paduvalp@gmail.com',
    'to_name'       => 'PNT Tech',
];
