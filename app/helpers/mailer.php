<?php
/**
 * Mailer Helper
 * ─────────────
 * Central email sending function using PHPMailer via SMTP.
 * Falls back to PHP mail() if SMTP is not configured.
 *
 * Usage:
 *   send_mail('customer@example.com', 'Subject', '<p>HTML body</p>');
 */

// Autoload PHPMailer
require_once APP_PATH . '/lib/PHPMailer/Exception.php';
require_once APP_PATH . '/lib/PHPMailer/PHPMailer.php';
require_once APP_PATH . '/lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception;

/**
 * Send an HTML email.
 *
 * @param string $toEmail    Recipient email
 * @param string $subject    Email subject
 * @param string $htmlBody   HTML email body
 * @param string $toName     Optional recipient name
 * @param string $replyTo    Optional reply-to email
 * @return bool              True on success, false on failure
 * @throws Exception
 */
function send_mail(string $toEmail, string $subject, string $htmlBody, string $toName = '', string $replyTo = ''): bool
{
    // ── Read SMTP settings ──────────────────────────────────────────────
    $smtpHost     = get_setting('smtp_host', '');
    $smtpPort     = (int) get_setting('smtp_port', '587');
    $smtpUser     = get_setting('smtp_username', '');
    $smtpPass     = get_setting('smtp_password', '');
    $smtpEncrypt  = get_setting('smtp_encryption', 'tls'); // tls | ssl | ''
    $fromEmail    = get_setting('smtp_from_email', get_setting('admin_email', 'noreply@localhost'));
    $fromName     = get_setting('smtp_from_name', get_setting('general_store_name', 'Store'));

    // ── Fallback: PHP mail() if SMTP not configured ─────────────────────
    if (empty($smtpHost) || empty($smtpUser)) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        if (!empty($replyTo)) {
            $headers .= "Reply-To: {$replyTo}\r\n";
        }
        return mail($toEmail, $subject, $htmlBody, $headers);
    }

    // ── PHPMailer via SMTP ───────────────────────────────────────────────
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->Port       = $smtpPort;

    if ($smtpEncrypt === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($smtpEncrypt === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPAutoTLS = false;
    }

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, $toName ?: $toEmail);

    if (!empty($replyTo)) {
        $mail->addReplyTo($replyTo);
    }

    $mail->isHTML(true);
    $mail->CharSet  = 'UTF-8';
    $mail->Subject  = $subject;
    $mail->Body     = $htmlBody;
    $mail->AltBody  = strip_tags($htmlBody);

    return $mail->send();
}
