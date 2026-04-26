<?php
/**
 * Frontend Checkout Controller
 * ────────────────────────────
 * Checkout page, coupon application, and order processing.
 */

class FrontendCheckoutController extends Controller
{
    /**
     * Show checkout page.
     */
    public function index(): void
    {
        $cartModel = new Cart();
        $cartData = $cartModel->getCartWithItems();

        if (empty($cartData['items'])) {
            Session::flash('error', 'Your cart is empty.');
            $this->redirect(url('/shop'));
            return;
        }

        $user = Auth::user();

        $this->render('frontend/checkout', [
            'page_title' => 'Checkout',
            'cart'       => $cartData,
            'user'       => $user,
            'discount'   => Session::get('checkout_discount', 0),
            'coupon'     => Session::get('checkout_coupon', null),
        ]);
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(): void
    {
        Middleware::verifyCsrf();

        $code = trim($this->post('coupon_code', ''));
        if (empty($code)) {
            Session::flash('error', 'Please enter a coupon code.');
            $this->redirect(url('/checkout'));
            return;
        }

        $cartModel = new Cart();
        $cartData = $cartModel->getCartWithItems();

        $couponModel = new Coupon();
        $result = $couponModel->validateCoupon($code, $cartData['subtotal']);

        if ($result['valid']) {
            Session::set('checkout_coupon', $result['coupon']);
            Session::set('checkout_discount', $result['discount']);
            Session::flash('success', 'Coupon applied! You save ' . formatPrice($result['discount']));
        } else {
            Session::remove('checkout_coupon');
            Session::remove('checkout_discount');
            Session::flash('error', $result['error']);
        }

        $this->redirect(url('/checkout'));
    }

    /**
     * Process checkout — create order + payment.
     */
    public function process(): void
    {
        Middleware::verifyCsrf();

        $cartModel = new Cart();
        $cartData = $cartModel->getCartWithItems();

        if (empty($cartData['items'])) {
            Session::flash('error', 'Your cart is empty.');
            $this->redirect(url('/shop'));
            return;
        }

        // Collect shipping address
        $shippingName = trim($this->post('shipping_name', ''));
        $shippingPhone = trim($this->post('shipping_phone', ''));
        $shippingAddress = trim($this->post('shipping_address', ''));
        $shippingCity = trim($this->post('shipping_city', ''));
        $shippingState = trim($this->post('shipping_state', ''));
        $shippingPincode = trim($this->post('shipping_pincode', ''));
        $paymentMethod = trim($this->post('payment_method', 'cod'));

        // Validate
        if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress) || empty($shippingCity) || empty($shippingState) || empty($shippingPincode)) {
            Session::flash('error', 'Please fill in all shipping details.');
            $this->redirect(url('/checkout'));
            return;
        }

        // Calculate totals
        $subtotal = $cartData['subtotal'];
        $discount = Session::get('checkout_discount', 0);
        $shippingFee = 0; // Free shipping for now
        $totalAmount = $subtotal - $discount + $shippingFee;

        // Coupon info
        $coupon = Session::get('checkout_coupon', null);
        $couponId = $coupon ? $coupon['id'] : null;

        // Build order data
        $orderData = [
            'user_id'          => Auth::id(),
            'subtotal'         => $subtotal,
            'discount_amount'  => $discount,
            'coupon_id'        => $couponId,
            'shipping_fee'     => $shippingFee,
            'total_amount'     => $totalAmount,
            'status'           => 'pending',
            'payment_status'   => ($paymentMethod === 'cod') ? 'pending' : 'pending',
            'payment_method'   => $paymentMethod,
            'shipping_name'    => $shippingName,
            'shipping_phone'   => $shippingPhone,
            'shipping_address' => $shippingAddress,
            'shipping_city'    => $shippingCity,
            'shipping_state'   => $shippingState,
            'shipping_pincode' => $shippingPincode,
        ];

        // Build order items
        $orderItems = [];
        foreach ($cartData['items'] as $item) {
            $orderItems[] = [
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_image'=> $item['product_image'] ?? '',
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
                'total'        => $item['price'] * $item['quantity'],
            ];
        }

        try {
            $orderModel = new Order();
            $orderId = $orderModel->createOrder($orderData, $orderItems);

            // Deduct stock
            $inventoryModel = new Inventory();
            foreach ($cartData['items'] as $item) {
                $inventoryModel->deductOnSale($item['product_id'], $item['quantity'], $orderId);
            }

            // Increment coupon usage
            if ($couponId) {
                $couponModel = new Coupon();
                $couponModel->incrementUsage($couponId);
            }

            // Create payment record
            $paymentModel = new Payment();
            $paymentModel->createPayment([
                'order_id'       => $orderId,
                'method'         => $paymentMethod,
                'amount'         => $totalAmount,
                'payment_status' => ($paymentMethod === 'cod') ? 'pending' : 'pending',
            ]);

            // Clear cart and checkout session
            $cartModel->clearCart();
            Session::remove('checkout_discount');
            Session::remove('checkout_coupon');

            Session::flash('success', 'Order placed successfully!');
            $this->redirect(url('/order/success/' . $orderId));
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong. Please try again.');
            $this->redirect(url('/checkout'));
        }
    }
}
