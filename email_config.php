<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer (standalone version)
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yourgmail@gmail.com';        // your Gmail
        $mail->Password   = 'your-app-password';          // App Password ONLY
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Disable debug logger to avoid PSR errors
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'error_log';

        // Email headers
        $mail->setFrom('yourgmail@gmail.com', 'Mentor System');
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Mailer Error: " . $e->getMessage();
    }
}
