<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If installed via Composer
// require 'path/to/PHPMailer/src/PHPMailer.php'; // Manual include
// require 'path/to/PHPMailer/src/SMTP.php';
// require 'path/to/PHPMailer/src/Exception.php';

function sendSMTPMail($to, $subject, $bodyHtml, $bodyPlain = '') {
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = $host; // your SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = $username; // your SMTP username
        $mail->Password   = $password;  // your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // or PHPMailer::ENCRYPTION_SMTPS
        $mail->Port       = 465; // usually 587 for TLS or 465 for SSL

        // Email settings
        $mail->setFrom($username, 'RAD5 Payment Voucher');
        $addresses = explode(',', $to);
        if (count($addresses) > 1) {
            foreach ($addresses as $address) {
                $mail->addAddress($address);
            }
        } else {
            $mail->addAddress($to); // Add recipient
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = $bodyPlain ?: strip_tags($bodyHtml);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}
