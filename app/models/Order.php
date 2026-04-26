<?php
/**
 * Order Model
 * ───────────
 * Manages customer orders, status updates, and order queries.
 * Table: orders
 */

class Order extends Model
{
    protected string $table = 'orders';

    /**
     * Generate a unique order number: ORD-YYYYMMDD-XXXX
     */
    public function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $prefix = "ORD-{$date}-";

        $stmt = Database::getInstance()->prepare(
            "SELECT order_number FROM orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();

        if ($last) {
            $lastNum = (int) substr($last, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new order from checkout data.
     */
    public function createOrder(array $orderData, array $items): int
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $orderData['order_number'] = $this->generateOrderNumber();
            $orderId = $this->create($orderData);

            // Insert order items
            $itemModel = new OrderItem();
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $itemModel->create($item);
            }

            // Add initial tracking entry
            $tracking = new OrderTracking();
            $tracking->addEntry($orderId, 'Pending', 'Order has been placed successfully.');

            $db->commit();
            return $orderId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get order with all details (items, addresses, tracking, payment).
     */
    public function getOrderDetail(int $orderId): array|false
    {
        $order = $this->find($orderId);
        if (!$order) return false;

        $order['items'] = (new OrderItem())->getByOrder($orderId);
        $order['tracking'] = (new OrderTracking())->getByOrder($orderId);

        // Get customer info
        if ($order['user_id']) {
            $user = new User();
            $order['customer'] = $user->find($order['user_id']);
        }

        // Get payment info
        $payment = new Payment();
        $order['payment'] = $payment->findWhere('order_id = ?', [$orderId]);

        return $order;
    }

    /**
     * Find order by order number.
     */
    public function findByOrderNumber(string $orderNumber): array|false
    {
        return $this->findWhere('order_number = ?', [$orderNumber]);
    }

    /**
     * Get orders for a user.
     */
    public function getUserOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->paginate($page, $perPage, 'created_at', 'DESC', 'user_id = ?', [$userId]);
    }

    /**
     * Get orders for admin with filters.
     */
    public function getAdminOrders(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['payment_status'])) {
            $where[] = 'o.payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(o.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(o.created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE {$whereClause}
                ORDER BY o.created_at DESC
                LIMIT ? OFFSET ?";
        $data = $this->query($sql, array_merge($params, [$perPage, $offset]))->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'pages'        => (int) ceil($total / $perPage),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, string $status, ?int $adminId = null): bool
    {
        $result = $this->update($orderId, ['status' => $status]);

        // Add tracking entry
        $tracking = new OrderTracking();
        $tracking->addEntry($orderId, ucfirst($status), "Order status updated to {$status}.", $adminId);

        return $result;
    }

    /**
     * Get order stats for dashboard.
     */
    public function getStats(): array
    {
        $db = Database::getInstance();

        $total = $this->count();
        $pending = $this->count("status = 'pending'");
        $confirmed = $this->count("status = 'confirmed'");
        $processing = $this->count("status IN ('packed', 'shipped')");
        $delivered = $this->count("status = 'delivered'");
        $cancelled = $this->count("status = 'cancelled'");

        // Revenue
        $stmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'");
        $stmt->execute();
        $totalRevenue = (float) $stmt->fetchColumn();

        // This month revenue
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(total_amount), 0) FROM orders
             WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
        );
        $stmt->execute();
        $monthRevenue = (float) $stmt->fetchColumn();

        // Today's orders
        $todayOrders = $this->count("DATE(created_at) = CURDATE()");

        return [
            'total'          => $total,
            'pending'        => $pending,
            'confirmed'      => $confirmed,
            'processing'     => $processing,
            'delivered'      => $delivered,
            'cancelled'      => $cancelled,
            'total_revenue'  => $totalRevenue,
            'month_revenue'  => $monthRevenue,
            'today_orders'   => $todayOrders,
        ];
    }

    /**
     * Get recent orders for dashboard.
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT o.*, u.name as customer_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT ?";
        return $this->query($sql, [$limit])->fetchAll();
    }

    /**
     * Get sales data for chart (last N days).
     */
    public function getSalesChart(int $days = 30): array
    {
        $sql = "SELECT DATE(created_at) as date,
                       COUNT(*) as order_count,
                       COALESCE(SUM(total_amount), 0) as revenue
                FROM orders
                WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        return $this->query($sql, [$days])->fetchAll();
    }
    /**
     * Get monthly sales data for the chart.
     */
    public function getMonthlySales(int $months = 6): array
    {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
                       COUNT(*) as total_orders,
                       COALESCE(SUM(total_amount), 0) as revenue
                FROM orders
                WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY month
                ORDER BY month ASC";
        return $this->query($sql, [$months])->fetchAll();
    }
}
