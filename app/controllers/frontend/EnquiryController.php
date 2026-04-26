<?php
/**
 * Frontend Enquiry Controller
 * ───────────────────────────
 * Handles enquiry submissions from the Enquiry Cart Modal.
 */

class FrontendEnquiryController extends Controller
{
    public function submit(): void
    {
        Middleware::verifyCsrf();

        $name = trim($this->post('customer_name', ''));
        $email = trim($this->post('customer_email', ''));
        $phone = trim($this->post('customer_phone', ''));
        $company = trim($this->post('customer_company', ''));
        $message = trim($this->post('message', ''));
        $wantsWhatsapp = $this->post('wants_whatsapp') ? 1 : 0;

        if (empty($name) || empty($email) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Name, Email, and Phone are required.']);
            exit;
        }

        $cartModel = new Cart();
        $cartData = $cartModel->getCartWithItems();

        if (empty($cartData['items'])) {
            echo json_encode(['success' => false, 'message' => 'Your enquiry cart is empty.']);
            exit;
        }

        // Snapshot cart items
        $snapshot = [];
        foreach ($cartData['items'] as $item) {
            $snapshot[] = [
                'product_name' => $item['product_name'],
                'product_code' => $item['product_code'] ?? $item['product_sku'] ?? '',
                'sku'          => $item['product_sku'] ?? '',
                'quantity'     => (int) $item['quantity'],
            ];
        }

        // Generate Enquiry Number
        $db = Database::getInstance();
        $stmt = $db->query("SELECT MAX(id) as max_id FROM enquiries");
        $nextId = ($stmt->fetch()['max_id'] ?? 0) + 1;
        $enqNumber = 'ENQ-' . date('Ymd') . '-' . str_pad((string)$nextId, 3, '0', STR_PAD_LEFT);

        // Insert into database
        $stmt = $db->prepare("
            INSERT INTO enquiries 
            (enquiry_number, customer_name, customer_email, customer_phone, customer_company, message, cart_snapshot) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $enqNumber,
            $name,
            $email,
            $phone,
            $company,
            $message,
            json_encode($snapshot)
        ]);
        
        $enquiryId = $db->lastInsertId();

        // Clear cart
        $cartModel->clearCart();

        // Send Email to Admin
        $adminEmail = get_setting('admin_email');
        if (!empty($adminEmail)) {
            $subject = "New Spare Parts Enquiry — {$enqNumber} from {$name}";
            $itemsHtml = "";
            foreach ($snapshot as $item) {
                $itemsHtml .= "<tr>
                    <td style='padding:8px; border:1px solid #ddd;'>{$item['product_name']}</td>
                    <td style='padding:8px; border:1px solid #ddd;'>{$item['product_code']}</td>
                    <td style='padding:8px; border:1px solid #ddd;'>{$item['quantity']}</td>
                </tr>";
            }
            
            $body = "
                <h3>New Enquiry Received</h3>
                <p><strong>Enquiry Number:</strong> {$enqNumber}</p>
                <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                    <tr><td style='padding:8px; border:1px solid #ddd;'><strong>Name</strong></td><td style='padding:8px; border:1px solid #ddd;'>{$name}</td></tr>
                    <tr><td style='padding:8px; border:1px solid #ddd;'><strong>Email</strong></td><td style='padding:8px; border:1px solid #ddd;'>{$email}</td></tr>
                    <tr><td style='padding:8px; border:1px solid #ddd;'><strong>Phone</strong></td><td style='padding:8px; border:1px solid #ddd;'>{$phone}</td></tr>
                    <tr><td style='padding:8px; border:1px solid #ddd;'><strong>Company</strong></td><td style='padding:8px; border:1px solid #ddd;'>{$company}</td></tr>
                </table>
                <br>
                <p><strong>Message/Requirements:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                <h4>Requested Items</h4>
                <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
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
                <br>
                <p><a href='" . url("/admin/enquiries/{$enquiryId}") . "'>View in Admin Panel</a></p>
            ";

            try {
                if (send_mail($adminEmail, $subject, $body)) {
                    $db->prepare("UPDATE enquiries SET admin_email_sent = 1 WHERE id = ?")->execute([$enquiryId]);
                }
            } catch (Exception $e) {
                error_log('Enquiry admin email failed: ' . $e->getMessage());
            }
        }

        // WhatsApp generation
        $whatsappUrl = "";
        if ($wantsWhatsapp) {
            $adminWhatsapp = get_setting('admin_whatsapp');
            if (!empty($adminWhatsapp)) {
                $waText = "New Enquiry {$enqNumber} from {$name} ({$phone}):\n";
                foreach ($snapshot as $item) {
                    $waText .= "- {$item['quantity']}x {$item['product_name']}\n";
                }
                $whatsappUrl = "https://wa.me/" . urlencode($adminWhatsapp) . "?text=" . urlencode($waText);
            }
        }

        echo json_encode([
            'success' => true,
            'enquiry_number' => $enqNumber,
            'whatsapp_url' => $whatsappUrl
        ]);
        exit;
    }
}
