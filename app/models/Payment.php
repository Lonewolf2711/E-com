<?php
/**
 * Payment Model
 * ─────────────
 * Manages payment records and gateway transactions.
 * Table: payments
 */

class Payment extends Model
{
    protected string $table = 'payments';

    /**
     * Create a payment record.
     */
    public function createPayment(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Update payment status after gateway callback.
     */
    public function updatePaymentStatus(int $paymentId, string $status, ?string $transactionId = null, ?string $gatewayResponse = null): bool
    {
        $data = ['payment_status' => $status];
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        return $this->update($paymentId, $data);
    }

    /**
     * Get payment by order ID.
     */
    public function getByOrder(int $orderId): array|false
    {
        return $this->findWhere('order_id = ?', [$orderId]);
    }

    /**
     * Get all payments with order details (admin).
     */
    public function getAdminPayments(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.payment_status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['method'])) {
            $where[] = 'p.method = ?';
            $params[] = $filters['method'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(p.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(p.created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM payments p WHERE {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $sql = "SELECT p.*, o.order_number, u.name as customer_name
                FROM payments p
                LEFT JOIN orders o ON p.order_id = o.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE {$whereClause}
                ORDER BY p.created_at DESC
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
     * Get payment method distribution for reports.
     */
    public function getMethodDistribution(): array
    {
        $sql = "SELECT method, COUNT(*) as count, COALESCE(SUM(amount), 0) as total
                FROM payments WHERE payment_status = 'success'
                GROUP BY method ORDER BY total DESC";
        return $this->query($sql)->fetchAll();
    }
}
