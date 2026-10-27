<?php
/*
 * SMTP settings for contact.php
 *
 * Copy this file to mail_config.php (next to contact.php) on the server and
 * fill in real values. mail_config.php is ignored by git and is not part of
 * the cPanel deployment, so the password never ends up in the repository.
 */
return [
    // SMTP server.
    // If the site is behind Cloudflare, do NOT use mail.yourdomain.com when that
    // record is proxied (orange cloud): SMTP cannot pass through Cloudflare and
    // the request hangs until Cloudflare shows error 524. Use one of these:
    //   - the mail server on the same cPanel host:
    //       'smtp_host' => 'localhost', 'smtp_port' => 25, 'smtp_secure' => 'none'
    //   - the server hostname from cPanel > Email Accounts > Connect Devices
    //     (e.g. server123.yourhost.com), port 465 with 'ssl'
    'smtp_host'     => 'mail.example.com',
    'smtp_port'     => 465,              // 465 = SSL, 587 = STARTTLS, 25 = localhost
    'smtp_secure'   => 'ssl',            // 'ssl', 'tls' or 'none' (localhost only)
    'smtp_timeout'  => 15,               // seconds before giving up on the SMTP server
    'smtp_username' => 'no-reply@example.com',
    'smtp_password' => 'CHANGE_ME',

    // Sender shown on the email (should be the SMTP account above)
    'from_email'    => 'no-reply@example.com',
    'from_name'     => 'PNT Tech Website',

    // Where contact form messages are delivered
    'to_email'      => 'paduvalp@gmail.com',
    'to_name'       => 'PNT Tech',
];
