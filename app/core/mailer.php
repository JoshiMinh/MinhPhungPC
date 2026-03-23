<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Get a pre-configured PHPMailer instance.
 * SMTP settings are pulled from the .env file via core/config.php.
 * 
 * @return PHPMailer
 * @throws Exception
 */
function getMailer() {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.example.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER') ?: 'your_email@example.com';
    $mail->Password   = getenv('SMTP_PASS') ?: 'your_email_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('SMTP_PORT') ?: 587;
    $mail->CharSet    = 'UTF-8';

    // Default sender
    $mail->setFrom($mail->Username, 'MinhPhungPC');

    return $mail;
}

/**
 * Send an email using PHPMailer.
 * 
 * @param string $to Email address
 * @param string $name Name of the recipient
 * @param string $subject Email subject
 * @param string $body HTML body
 * @param string $altBody Plain text body
 * @return bool True on success, false on failure
 */
function sendEmail($to, $name, $subject, $body, $altBody = '') {
    try {
        $mail = getMailer();
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
}
?>