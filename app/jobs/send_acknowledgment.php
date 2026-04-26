<?php
/**
 * Cron Job: Send Acknowledgment Emails
 * ────────────────────────────────────
 * Execute via: * * * * * php /path/to/send_acknowledgment.php
 */

// ─── Bootstrap ───
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once CONFIG_PATH . '/app.php';
require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/helpers/functions.php';

// Initialize the Database
$db = Database::getInstance();

$storeName = get_setting('general_store_name', 'Our Store');
$contactPhone = get_setting('contact_phone', '');
$contactWhatsapp = get_setting('contact_whatsapp', '');
$contactEmail = get_setting('contact_email', '');
$contactAddress = get_setting('contact_address', '');

$logFile = ROOT_PATH . '/logs/email_log.txt';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0777, true);
}

function logMessage(string $msg) {
    global $logFile;
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$date}] {$msg}\n", FILE_APPEND);
}

logMessage("Started send_acknowledgment.php cron job.");

$stmt = $db->query("
    SELECT * FROM enquiries 
    WHERE customer_email_sent = 0 
    AND created_at <= NOW() - INTERVAL 1 MINUTE
");

$enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$enquiries) {
    logMessage("No pending acknowledgments found.");
    exit;
}

$fromEmail = "no-reply@" . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: {$storeName} <{$fromEmail}>\r\n";

$successCount = 0;

foreach ($enquiries as $enq) {
    $snapshot = json_decode($enq['cart_snapshot'], true);
    
    $itemsHtml = "";
    if (is_array($snapshot)) {
        foreach ($snapshot as $item) {
            $itemsHtml .= "<tr>
                <td style='padding:8px; border:1px solid #ddd;'>{$item['product_name']}</td>
                <td style='padding:8px; border:1px solid #ddd;'>{$item['product_code']}</td>
                <td style='padding:8px; border:1px solid #ddd;'>{$item['quantity']}</td>
            </tr>";
        }
    }

    $subject = "We've received your enquiry {$enq['enquiry_number']} — {$storeName}";
    $body = "
        <p>Dear {$enq['customer_name']},</p>
        <p>Thank you for your enquiry regarding spare parts.</p>
        <p>Here is a summary of the items you requested:</p>
        <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin-bottom: 20px;'>
            <thead>
                <tr style='background:#f9f9f9;'>
                    <th style='padding:8px; border:1px solid #ddd;'>Product Name</th>
                    <th style='padding:8px; border:1px solid #ddd;'>Product Code</th>
                    <th style='padding:8px; border:1px solid #ddd;'>Quantity</th>
                </tr>
            </thead>
            <tbody>
                {$itemsHtml}
            </tbody>
        </table>
        <p><strong>Our team will review your requirements and send a quotation within 24 hours.</strong></p>
        <div style='background: #f1f1f1; padding: 15px; margin-top: 20px;'>
            <h4>Contact Details</h4>
            <p><strong>Phone:</strong> {$contactPhone}<br>
               <strong>WhatsApp:</strong> {$contactWhatsapp}<br>
               <strong>Email:</strong> {$contactEmail}<br>
               <strong>Address:</strong> {$contactAddress}
            </p>
        </div>
        <p style='font-size: 18px; margin-top: 15px;'><strong>Call us now: {$contactPhone}</strong></p>
    ";

    // Send email
    if (mail($enq['customer_email'], $subject, $body, $headers)) {
        // Update DB
        $db->prepare("
            UPDATE enquiries 
            SET customer_email_sent = 1, status = 'acknowledged' 
            WHERE id = ?
        ")->execute([$enq['id']]);
        
        logMessage("Sent acknowledgment for Enquiry {$enq['enquiry_number']} to {$enq['customer_email']}.");
        $successCount++;
    } else {
        logMessage("Failed to send acknowledgment to {$enq['customer_email']} for Enquiry {$enq['enquiry_number']}.");
    }
}

logMessage("Completed. Successfully sent: {$successCount}.");
