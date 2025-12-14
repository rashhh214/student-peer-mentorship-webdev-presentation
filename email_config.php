<?php
/**
 * Email Configuration for Student Peer Mentorship System
 * Using PHPMailer with Gmail SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @return bool|string True on success, error message on failure
 */
function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        //========================================
        // SMTP Configuration
        //========================================
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // ⚠️ IMPORTANT: Change these to your Gmail credentials
        $mail->Username   = 'rasheedomar194@gmail.com';           // Your Gmail address
        $mail->Password   = 'sqqq kewr untu dtvv';            // Your 16-char App Password
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //========================================
        // Debug Settings (0 = off, 2 = verbose)
        //========================================
        $mail->SMTPDebug  = 0;                                // Set to 2 for troubleshooting
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug ($level): $str");
        };

        //========================================
        // Email Headers
        //========================================
        // ⚠️ IMPORTANT: Change this to your Gmail address
        $mail->setFrom('rasheedomar194@gmail.com', 'Student Peer Mentorship System');
        $mail->addAddress($to);
        $mail->addReplyTo('your-email@gmail.com', 'Student Peer Mentorship System');

        //========================================
        // Email Content
        //========================================
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);  // Plain text version

        //========================================
        // Send Email
        //========================================
        $mail->send();
        
        // Log success
        error_log("[EMAIL SUCCESS] Sent to: $to | Subject: $subject");
        return true;

    } catch (Exception $e) {
        // Log error details
        $error_msg = "Email Error: {$mail->ErrorInfo}";
        error_log("[EMAIL FAILED] To: $to | Subject: $subject | Error: $error_msg");
        
        // Return error message
        return $error_msg;
    }
}

/**
 * ============================================================================
 * SETUP INSTRUCTIONS:
 * ============================================================================
 * 
 * 1. GET GMAIL APP PASSWORD:
 *    - Go to: https://myaccount.google.com/apppasswords
 *    - Enable 2-Step Verification if not enabled
 *    - Generate App Password for "Mail" / "Other"
 *    - Copy the 16-character password (e.g., abcd efgh ijkl mnop)
 * 
 * 2. UPDATE THIS FILE:
 *    - Line 32: Replace 'your-email@gmail.com' with your Gmail
 *    - Line 33: Replace 'xxxx xxxx xxxx xxxx' with your App Password
 *    - Line 46: Replace 'your-email@gmail.com' with your Gmail
 *    - Line 47: Replace 'your-email@gmail.com' with your Gmail
 * 
 * 3. PHPMAILER FILES:
 *    Make sure these files exist:
 *    - phpmailer/Exception.php
 *    - phpmailer/PHPMailer.php
 *    - phpmailer/SMTP.php
 * 
 * 4. TEST:
 *    Create test_email.php and send a test email to verify setup
 * 
 * ============================================================================
 * TROUBLESHOOTING:
 * ============================================================================
 * 
 * If emails not sending:
 * - Verify Gmail App Password is correct (no extra spaces)
 * - Check 2-Step Verification is enabled on Gmail
 * - Enable openssl in php.ini: extension=openssl
 * - Check error log: C:\xampp2\apache\logs\error.log
 * - Set SMTPDebug to 2 (line 41) to see detailed output
 * 
 * Common errors:
 * - "SMTP connect() failed" = Wrong credentials or firewall blocking
 * - "Could not authenticate" = Wrong App Password
 * - "Extension openssl not loaded" = Enable openssl in php.ini
 * 
 * ============================================================================
 */
?>