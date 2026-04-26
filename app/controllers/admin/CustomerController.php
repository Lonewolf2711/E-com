<?php
/**
 * Admin Customer Controller
 * ─────────────────────────
 * Manages customer accounts, viewing details, and updating status (ban/unban).
 */

class AdminCustomerController extends Controller
{
    private User $userModel;
    private Order $orderModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    /**
     * List all customers with pagination and search.
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $search = $this->get('search', '');

        if ($search) {
            $data = $this->userModel->searchCustomers($search);
            $customers = [
                'data' => $data,
                'total' => count($data),
                'pages' => 1,
                'current_page' => 1
            ];
        } else {
            $customers = $this->userModel->getCustomers($page, 15);
        }

        $stats = $this->userModel->getCustomerStats();

        $this->render('admin/customers/index', [
            'page_title' => 'Customers',
            'customers'  => $customers,
            'stats'      => $stats,
            'search'     => $search,
        ]);
    }

    /**
     * View customer details (profile, orders, reviews).
     */
    public function detail(int $id): void
    {
        $customer = $this->userModel->find($id);
        
        if (!$customer || $customer['role'] !== 'customer') {
            $this->redirect('/admin/customers', 'Customer not found.', 'error');
        }

        // Get customer's orders
        $page = (int) $this->get('page', 1);
        $orders = $this->orderModel->getUserOrders($id, $page, 10);

        // Get lifetime stats for this customer
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_spent FROM orders WHERE user_id = ? AND payment_status = 'paid'");
        $stmt->execute([$id]);
        $customerStats = $stmt->fetch();

        $this->render('admin/customers/detail', [
            'page_title'    => 'Customer Details',
            'customer'      => $customer,
            'orders'        => $orders,
            'customerStats' => $customerStats
        ]);
    }

    /**
     * Update customer status (active/banned).
     */
    public function updateStatus(int $id): void
    {
        $status = $this->post('status');
        
        if (!in_array($status, ['active', 'banned'])) {
            $this->redirect('/admin/customers/' . $id, 'Invalid status.', 'error');
        }

        if ($this->userModel->updateStatus($id, $status)) {
            $this->redirect('/admin/customers/' . $id, 'Customer status updated successfully.', 'success');
        } else {
            $this->redirect('/admin/customers/' . $id, 'Failed to update status.', 'error');
        }
    }
}
