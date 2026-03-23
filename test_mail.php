<?php
require 'app/core/mailer.php';

// Test configuration - replace with real ones in .env for actual use
$testEmail = 'recipient@example.com';
$testName  = 'Test Recipient';
$subject   = 'Test Email from MinhPhungPC';
$body      = '<h1>Success!</h1><p>PHPMailer is working from the root directory.</p>';

echo "Attempting to send test email...\n";

$result = sendEmail($testEmail, $testName, $subject, $body);

if ($result === true) {
    echo "Test email sent successfully!\n";
} else {
    echo "Failed to send test email: $result\n";
}
?>
