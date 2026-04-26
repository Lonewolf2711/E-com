<?php
/**
 * Admin Order Controller
 * ──────────────────────
 * Order list, detail, status updates, and tracking management.
 */

class AdminOrderController extends Controller
{
    /**
     * List all orders with filtering.
     */
    public function index(): void
    {
        $orderModel = new Order();
        $page = (int) ($this->get('page') ?: 1);
        $status = $this->get('status') ?: '';
        $search = $this->get('q') ?: '';
        $paymentStatus = $this->get('payment_status') ?: '';
        $dateFrom = $this->get('date_from') ?: '';
        $dateTo = $this->get('date_to') ?: '';

        $filters = [
            'status'         => $status,
            'search'         => $search,
            'payment_status' => $paymentStatus,
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
        ];

        $orders = $orderModel->getAdminOrders($filters, $page, 20);

        $this->render('admin/orders/index', [
            'page_title' => 'Orders',
            'orders'     => $orders,
            'filters'    => $filters,
        ]);
    }

    /**
     * Show order detail.
     */
    public function detail(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderDetail($id);

        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('/admin/orders'));
            return;
        }

        // Get order items
        $itemModel = new OrderItem();
        $items = $itemModel->getByOrder($id);

        // Get tracking history
        $trackingModel = new OrderTracking();
        $tracking = $trackingModel->getByOrder($id);

        // Get payment info
        $paymentModel = new Payment();
        $payment = $paymentModel->getByOrder($id);

        $this->render('admin/orders/detail', [
            'page_title' => 'Order #' . $order['order_number'],
            'order'      => $order,
            'items'      => $items,
            'tracking'   => $tracking,
            'payment'    => $payment,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('/admin/orders'));
            return;
        }

        $newStatus = $this->post('status', '');
        $validStatuses = ['pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($newStatus, $validStatuses)) {
            Session::flash('error', 'Invalid status.');
            $this->redirect(url('/admin/orders/' . $id));
            return;
        }

        $orderModel->update($id, ['status' => $newStatus]);

        // Auto-create tracking entry
        $trackingModel = new OrderTracking();
        $messages = [
            'confirmed' => 'Order has been confirmed by the seller.',
            'packed'    => 'Order has been packed and is ready for shipping.',
            'shipped'   => 'Order has been shipped.',
            'delivered' => 'Order has been delivered successfully.',
            'cancelled' => 'Order has been cancelled.',
        ];

        $trackingModel->create([
            'order_id' => $id,
            'status'   => $newStatus,
            'message'  => $messages[$newStatus] ?? 'Order status updated to ' . $newStatus . '.',
        ]);

        // Auto-update payment status for delivered or cancelled
        if ($newStatus === 'delivered' && $order['payment_method'] === 'cod') {
            $paymentModel = new Payment();
            $payment = $paymentModel->getByOrder($id);
            if ($payment) {
                $paymentModel->update($payment['id'], ['status' => 'paid']);
            }
            $orderModel->update($id, ['payment_status' => 'paid']);
        }

        Session::flash('success', 'Order status updated to ' . ucfirst($newStatus) . '.');
        $this->redirect(url('/admin/orders/' . $id));
    }

    /**
     * Add a custom tracking entry.
     */
    public function addTracking(int $id): void
    {
        $orderModel = new Order();
        $order = $orderModel->find($id);

        if (!$order) {
            Session::flash('error', 'Order not found.');
            $this->redirect(url('/admin/orders'));
            return;
        }

        $status = trim($this->post('tracking_status', ''));
        $message = trim($this->post('tracking_message', ''));

        if (empty($status) || empty($message)) {
            Session::flash('error', 'Status and message are required.');
            $this->redirect(url('/admin/orders/' . $id));
            return;
        }

        $trackingModel = new OrderTracking();
        $trackingModel->create([
            'order_id' => $id,
            'status'   => $status,
            'message'  => $message,
        ]);

        Session::flash('success', 'Tracking entry added.');
        $this->redirect(url('/admin/orders/' . $id));
    }
}
