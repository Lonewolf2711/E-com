<?php
/**
 * Admin AI Controller
 * ───────────────────
 * Controller for Groq AI integrations (like Quote email generation).
 */

class AdminAiController extends Controller
{
    /**
     * POST endpoint to generate an email draft from Groq AI.
     */
    public function generateQuoteEmail(): void
    {
        // Read raw JSON body (sent by fetch with Content-Type: application/json)
        $rawInput  = file_get_contents('php://input');
        $jsonInput = json_decode($rawInput, true) ?? [];

        // Validate CSRF from JSON body (token sent as _token field)
        $token        = $jsonInput['_token'] ?? '';
        $sessionToken = Session::get('csrf_token', '');
        if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        // 1. Gather payload from decoded JSON
        $enquiryId = (int) ($jsonInput['enquiry_id'] ?? 0);
        $tone      = trim($jsonInput['tone']  ?? 'formal');
        $notes     = trim($jsonInput['notes'] ?? '');

        // 2. Validate
        if (!$enquiryId) {
            echo json_encode(['success' => false, 'message' => 'Missing Context.']);
            exit;
        }

        $enquiryModel = new Enquiry();
        $enquiry = $enquiryModel->getById($enquiryId);

        if (!$enquiry) {
            echo json_encode(['success' => false, 'message' => 'Enquiry not found.']);
            exit;
        }

        $apiKey = get_setting('groq_api_key');
        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'message' => 'Groq API Key is not configured in settings.']);
            exit;
        }

        // 3. Build the prompt
        // Extract items
        $snapshot = json_decode($enquiry['cart_snapshot'], true) ?: [];
        $itemList = "";
        foreach ($snapshot as $item) {
            $itemList .= "- {$item['quantity']}x {$item['product_name']} ({$item['product_code']})\n";
        }

        $contactPhone = get_setting('contact_phone', '');
        $contactEmail = get_setting('contact_email', '');
        $contactDetails = "Phone: {$contactPhone}, Email: {$contactEmail}";

        $prompt = "You are a professional sales representative for a spare parts company in India.
Write a quotation follow-up email to {$enquiry['customer_name']} regarding their enquiry {$enquiry['enquiry_number']}.
They enquired about:
{$itemList}
Their message: {$enquiry['message']}
Tone: {$tone}. 
Additional notes: {$notes}.
Include: greeting, reference to their specific parts, next steps, and company contact details.
Company contact: {$contactDetails}.
Keep it under 200 words. Output only the email body.";

        // 4. Require Groq helper & Execute
        require_once APP_PATH . '/helpers/groq.php';
        $body = groq_generate($prompt, $apiKey);

        // Filter out JSON wrapper or preambles just in case
        $body = trim($body);

        $subject = "Quotation regarding your Enquiry {$enquiry['enquiry_number']}";

        echo json_encode([
            'success' => true,
            'subject' => $subject,
            'body'    => $body
        ]);
        exit;
    }
}
