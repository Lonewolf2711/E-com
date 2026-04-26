<?php
/**
 * Order Tracking Model
 * ────────────────────
 * Manages order tracking timeline entries.
 * Table: order_tracking
 */

class OrderTracking extends Model
{
    protected string $table = 'order_tracking';

    /**
     * Get tracking timeline for an order.
     */
    public function getByOrder(int $orderId): array
    {
        $sql = "SELECT ot.*, u.name as admin_name
                FROM order_tracking ot
                LEFT JOIN users u ON ot.created_by = u.id
                WHERE ot.order_id = ?
                ORDER BY ot.created_at ASC";
        return $this->query($sql, [$orderId])->fetchAll();
    }

    /**
     * Add a tracking entry.
     */
    public function addEntry(int $orderId, string $status, string $message = '', ?int $createdBy = null, string $location = ''): int
    {
        return $this->create([
            'order_id'   => $orderId,
            'status'     => $status,
            'message'    => $message,
            'location'   => $location,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Get latest tracking status for an order.
     */
    public function getLatestStatus(int $orderId): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY created_at DESC LIMIT 1";
        return $this->query($sql, [$orderId])->fetch();
    }
}
