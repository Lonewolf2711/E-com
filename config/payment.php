<?php
/**
 * Payment Gateway Configuration
 * ─────────────────────────────
 * API keys and settings for each payment gateway.
 * Replace with your actual keys from each provider's dashboard.
 */

// ─── Razorpay (Primary Gateway) ───
// Dashboard: https://dashboard.razorpay.com
// Use test keys for development, live keys for production
define('RAZORPAY_ENABLED', true);
define('RAZORPAY_KEY_ID', '');          // rzp_test_xxxxxxxxxxxx
define('RAZORPAY_KEY_SECRET', '');      // Your Razorpay key secret
define('RAZORPAY_WEBHOOK_SECRET', '');  // Webhook signing secret from Dashboard → Webhooks

// ─── Stripe ───
// Dashboard: https://dashboard.stripe.com
define('STRIPE_ENABLED', false);
define('STRIPE_PUBLISHABLE_KEY', '');   // pk_test_xxxxxxxxxxxx
define('STRIPE_SECRET_KEY', '');        // sk_test_xxxxxxxxxxxx
define('STRIPE_WEBHOOK_SECRET', '');    // whsec_xxxxxxxxxxxx

// ─── PayPal ───
// Dashboard: https://developer.paypal.com
define('PAYPAL_ENABLED', false);
define('PAYPAL_MODE', 'sandbox');       // 'sandbox' or 'live'
define('PAYPAL_CLIENT_ID', '');         // PayPal app client ID
define('PAYPAL_CLIENT_SECRET', '');     // PayPal app secret

// ─── Cash on Delivery ───
define('COD_ENABLED', true);

// ─── Payment Currency ───
define('PAYMENT_CURRENCY', 'INR');
