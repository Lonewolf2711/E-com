<?php
/**
 * Frontend Order Controller
 * ─────────────────────────
 * Order success, tracking, my orders, order detail.
 */

class FrontendOrderController extends Controller
{
    /**
     * Order success page.
     */
    public function success(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderDetail($id);

        if (!$order || $order['user_id'] != Auth::id()) {
            $this->abort(404);
            return;
        }

        $this->render('frontend/order-success', [
            'page_title' => 'Order Confirmed',
            'order'      => $order,
        ]);
    }

    /**
     * Track order page.
     */
    public function track(): void
    {
        $orderNumber = trim($this->get('q') ?: '');
        $order = null;

        if ($orderNumber) {
            $orderModel = new Order();
            $order = $orderModel->findByOrderNumber($orderNumber);
            if ($order) {
                $trackingModel = new OrderTracking();
                $order['tracking'] = $trackingModel->getByOrder($order['id']);
            }
        }

        $this->render('frontend/track-order', [
            'page_title'   => 'Track Order',
            'order_number' => $orderNumber,
            'order'        => $order,
        ]);
    }

    /**
     * My orders list.
     */
    public function myOrders(): void
    {
        $orderModel = new Order();
        $page = (int) ($this->get('page') ?: 1);
        $orders = $orderModel->getUserOrders(Auth::id(), $page, 10);

        $this->render('frontend/my-orders', [
            'page_title' => 'My Orders',
            'orders'     => $orders,
        ]);
    }

    /**
     * Single order detail.
     */
    public function myOrderDetail(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderDetail($id);

        if (!$order || $order['user_id'] != Auth::id()) {
            $this->abort(404);
            return;
        }

        $this->render('frontend/order-detail', [
            'page_title' => 'Order #' . $order['order_number'],
            'order'      => $order,
        ]);
    }
}
