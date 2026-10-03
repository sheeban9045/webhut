<?php
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function smtp_send_mail($to, $subject, $body, $opts = array()) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($opts['from_email'] ?? SMTP_FROM_EMAIL, $opts['from_name'] ?? SMTP_FROM_NAME);
        $mail->addAddress($to);

        if (!empty($opts['reply_to'])) {
            $mail->addReplyTo($opts['reply_to'], $opts['reply_name'] ?? '');
        }
        // if (!empty($opts['message_id'])) {
        //     $mail->MessageID = $opts['message_id'];
        // }
        if (!empty($opts['in_reply_to'])) {
            $mail->addCustomHeader('In-Reply-To', $opts['in_reply_to']);
            $mail->addCustomHeader('References', $opts['references'] ?? $opts['in_reply_to']);
        }

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('SMTP mail failed to ' . $to . ': ' . $mail->ErrorInfo);
        return false;
    }
}