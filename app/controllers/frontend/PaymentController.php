<?php
/**
 * Payment Gateway Controller
 * ──────────────────────────
 * Handles Razorpay order creation, verification, webhooks,
 * Stripe checkout session creation + webhook,
 * PayPal order creation + capture.
 * All gateway-specific logic is self-contained here.
 */

class PaymentController extends Controller
{
    // ═══════════════════════════════════════════
    // RAZORPAY
    // ═══════════════════════════════════════════

    /**
     * Create a Razorpay order and return order_id to frontend JS.
     * Called via AJAX from checkout page.
     */
    public function razorpayCreateOrder(): void
    {
        if (!RAZORPAY_ENABLED) {
            $this->json(['error' => 'Razorpay is not enabled'], 400);
            return;
        }

        $amount = (float) $this->post('amount', 0);
        $orderId = (int) $this->post('order_id', 0);

        if ($amount <= 0 || $orderId <= 0) {
            $this->json(['error' => 'Invalid amount or order'], 400);
            return;
        }

        $amountInPaise = (int) ($amount * 100);

        // Razorpay Orders API
        $data = json_encode([
            'amount'   => $amountInPaise,
            'currency' => PAYMENT_CURRENCY,
            'receipt'  => 'order_' . $orderId,
        ]);

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->json(['error' => 'Failed to create Razorpay order'], 500);
            return;
        }

        $rzpOrder = json_decode($response, true);

        $this->json([
            'success'      => true,
            'razorpay_order_id' => $rzpOrder['id'],
            'key_id'       => RAZORPAY_KEY_ID,
            'amount'       => $amountInPaise,
            'currency'     => PAYMENT_CURRENCY,
        ]);
    }

    /**
     * Verify Razorpay payment after checkout.js callback.
     */
    public function razorpayVerify(): void
    {
        $razorpayPaymentId = $this->post('razorpay_payment_id', '');
        $razorpayOrderId   = $this->post('razorpay_order_id', '');
        $razorpaySignature = $this->post('razorpay_signature', '');
        $orderId           = (int) $this->post('order_id', 0);

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, RAZORPAY_KEY_SECRET);

        if ($expectedSignature !== $razorpaySignature) {
            $this->json(['error' => 'Payment verification failed'], 400);
            return;
        }

        // Payment verified → update records
        $paymentModel = new Payment();
        $payment = $paymentModel->getByOrder($orderId);

        if ($payment) {
            $paymentModel->updatePaymentStatus(
                $payment['id'],
                'paid',
                $razorpayPaymentId,
                json_encode([
                    'razorpay_order_id'   => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                ])
            );
        }

        // Update order payment status
        $orderModel = new Order();
        $orderModel->update($orderId, ['payment_status' => 'paid']);

        // Add tracking entry
        $trackingModel = new OrderTracking();
        $trackingModel->create([
            'order_id' => $orderId,
            'status'   => 'confirmed',
            'message'  => 'Payment received via Razorpay (ID: ' . $razorpayPaymentId . ').',
        ]);

        $this->json(['success' => true, 'redirect' => url('/order/success/' . $orderId)]);
    }

    /**
     * Razorpay webhook handler.
     */
    public function razorpayWebhook(): void
    {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

        // Verify webhook signature
        if (!empty(RAZORPAY_WEBHOOK_SECRET)) {
            $expectedSignature = hash_hmac('sha256', $payload, RAZORPAY_WEBHOOK_SECRET);
            if (!hash_equals($expectedSignature, $signature)) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid signature']);
                exit;
            }
        }

        $event = json_decode($payload, true);
        $eventType = $event['event'] ?? '';

        if ($eventType === 'payment.captured') {
            $paymentEntity = $event['payload']['payment']['entity'] ?? [];
            $receipt = $paymentEntity['notes']['receipt'] ?? '';
            $orderId = (int) str_replace('order_', '', $receipt);

            if ($orderId > 0) {
                $paymentModel = new Payment();
                $payment = $paymentModel->getByOrder($orderId);
                if ($payment) {
                    $paymentModel->updatePaymentStatus(
                        $payment['id'],
                        'paid',
                        $paymentEntity['id'] ?? '',
                        json_encode($paymentEntity)
                    );
                }
                $orderModel = new Order();
                $orderModel->update($orderId, ['payment_status' => 'paid']);
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // ═══════════════════════════════════════════
    // STRIPE
    // ═══════════════════════════════════════════

    /**
     * Create Stripe Checkout Session.
     */
    public function stripeCreateSession(): void
    {
        if (!STRIPE_ENABLED) {
            $this->json(['error' => 'Stripe is not enabled'], 400);
            return;
        }

        $amount = (float) $this->post('amount', 0);
        $orderId = (int) $this->post('order_id', 0);

        if ($amount <= 0 || $orderId <= 0) {
            $this->json(['error' => 'Invalid data'], 400);
            return;
        }

        $amountInSmallest = (int) ($amount * 100);

        $postData = http_build_query([
            'payment_method_types[]' => 'card',
            'line_items[0][price_data][currency]' => strtolower(PAYMENT_CURRENCY),
            'line_items[0][price_data][unit_amount]' => $amountInSmallest,
            'line_items[0][price_data][product_data][name]' => 'Order #' . $orderId,
            'line_items[0][quantity]' => 1,
            'mode' => 'payment',
            'success_url' => url('/payment/stripe/success?order_id=' . $orderId . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/payment/stripe/cancel?order_id=' . $orderId),
            'metadata[order_id]' => $orderId,
        ]);

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':',
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->json(['error' => 'Stripe session creation failed'], 500);
            return;
        }

        $session = json_decode($response, true);
        $this->json(['success' => true, 'session_url' => $session['url'] ?? '']);
    }

    /**
     * Stripe success return handler.
     */
    public function stripeSuccess(): void
    {
        $orderId = (int) $this->get('order_id', 0);
        $sessionId = $this->get('session_id', '');

        if ($orderId > 0 && !empty($sessionId)) {
            // Retrieve session from Stripe to confirm
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':',
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $session = json_decode($response, true);

            if (($session['payment_status'] ?? '') === 'paid') {
                $paymentModel = new Payment();
                $payment = $paymentModel->getByOrder($orderId);
                if ($payment) {
                    $paymentModel->updatePaymentStatus($payment['id'], 'paid', $session['payment_intent'] ?? $sessionId);
                }
                $orderModel = new Order();
                $orderModel->update($orderId, ['payment_status' => 'paid']);

                $trackingModel = new OrderTracking();
                $trackingModel->create([
                    'order_id' => $orderId,
                    'status'   => 'confirmed',
                    'message'  => 'Payment received via Stripe.',
                ]);
            }
        }

        $this->redirect(url('/order/success/' . $orderId));
    }

    /**
     * Stripe cancel return handler.
     */
    public function stripeCancel(): void
    {
        $orderId = (int) $this->get('order_id', 0);
        Session::flash('error', 'Payment was cancelled. You can try again.');
        $this->redirect(url('/checkout'));
    }

    /**
     * Stripe webhook handler.
     */
    public function stripeWebhook(): void
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        // Verify signature
        if (!empty(STRIPE_WEBHOOK_SECRET) && !empty($sigHeader)) {
            $parts = [];
            foreach (explode(',', $sigHeader) as $part) {
                [$key, $val] = explode('=', $part, 2);
                $parts[trim($key)] = trim($val);
            }
            $timestamp = $parts['t'] ?? '';
            $signedPayload = $timestamp . '.' . $payload;
            $expected = hash_hmac('sha256', $signedPayload, STRIPE_WEBHOOK_SECRET);
            if (!hash_equals($expected, $parts['v1'] ?? '')) {
                http_response_code(400);
                exit;
            }
        }

        $event = json_decode($payload, true);

        if (($event['type'] ?? '') === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];
            $orderId = (int) ($session['metadata']['order_id'] ?? 0);

            if ($orderId > 0 && ($session['payment_status'] ?? '') === 'paid') {
                $paymentModel = new Payment();
                $payment = $paymentModel->getByOrder($orderId);
                if ($payment) {
                    $paymentModel->updatePaymentStatus($payment['id'], 'paid', $session['payment_intent'] ?? '');
                }
                $orderModel = new Order();
                $orderModel->update($orderId, ['payment_status' => 'paid']);
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // ═══════════════════════════════════════════
    // PAYPAL
    // ═══════════════════════════════════════════

    /**
     * Create PayPal order.
     */
    public function paypalCreateOrder(): void
    {
        if (!PAYPAL_ENABLED) {
            $this->json(['error' => 'PayPal is not enabled'], 400);
            return;
        }

        $amount = (float) $this->post('amount', 0);
        $orderId = (int) $this->post('order_id', 0);

        if ($amount <= 0 || $orderId <= 0) {
            $this->json(['error' => 'Invalid data'], 400);
            return;
        }

        // Get access token
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            $this->json(['error' => 'PayPal auth failed'], 500);
            return;
        }

        $apiBase = PAYPAL_MODE === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $payload = json_encode([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'order_' . $orderId,
                'amount' => [
                    'currency_code' => PAYMENT_CURRENCY,
                    'value' => number_format($amount, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => url('/payment/paypal/success?order_id=' . $orderId),
                'cancel_url' => url('/payment/paypal/cancel?order_id=' . $orderId),
            ],
        ]);

        $ch = curl_init($apiBase . '/v2/checkout/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $ppOrder = json_decode($response, true);
        $approveUrl = '';
        foreach ($ppOrder['links'] ?? [] as $link) {
            if ($link['rel'] === 'approve') {
                $approveUrl = $link['href'];
                break;
            }
        }

        $this->json([
            'success'      => true,
            'paypal_order_id' => $ppOrder['id'] ?? '',
            'approve_url'  => $approveUrl,
        ]);
    }

    /**
     * PayPal success return — capture payment.
     */
    public function paypalSuccess(): void
    {
        $orderId = (int) $this->get('order_id', 0);
        $ppOrderId = $this->get('token', ''); // PayPal adds token param

        if (!empty($ppOrderId)) {
            $accessToken = $this->getPayPalAccessToken();
            $apiBase = PAYPAL_MODE === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $ch = curl_init($apiBase . '/v2/checkout/orders/' . $ppOrderId . '/capture');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => '{}',
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $capture = json_decode($response, true);

            if (($capture['status'] ?? '') === 'COMPLETED') {
                $captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';

                $paymentModel = new Payment();
                $payment = $paymentModel->getByOrder($orderId);
                if ($payment) {
                    $paymentModel->updatePaymentStatus($payment['id'], 'paid', $captureId, json_encode($capture));
                }
                $orderModel = new Order();
                $orderModel->update($orderId, ['payment_status' => 'paid']);

                $trackingModel = new OrderTracking();
                $trackingModel->create([
                    'order_id' => $orderId,
                    'status'   => 'confirmed',
                    'message'  => 'Payment received via PayPal.',
                ]);
            }
        }

        $this->redirect(url('/order/success/' . $orderId));
    }

    /**
     * PayPal cancel return handler.
     */
    public function paypalCancel(): void
    {
        Session::flash('error', 'PayPal payment was cancelled.');
        $this->redirect(url('/checkout'));
    }

    /**
     * Get PayPal OAuth2 access token.
     */
    private function getPayPalAccessToken(): string
    {
        $apiBase = PAYPAL_MODE === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $ch = curl_init($apiBase . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? '';
    }
}
