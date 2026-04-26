<?php
/**
 * Order Item Model
 * ────────────────
 * Manages individual items within an order.
 * Table: order_items
 */

class OrderItem extends Model
{
    protected string $table = 'order_items';

    /**
     * Get all items for an order with product details.
     */
    public function getByOrder(int $orderId): array
    {
        $sql = "SELECT oi.*, p.slug as product_slug, p.image as current_image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id ASC";
        return $this->query($sql, [$orderId])->fetchAll();
    }

    /**
     * Get total item count for an order.
     */
    public function getOrderItemCount(int $orderId): int
    {
        return $this->count('order_id = ?', [$orderId]);
    }

    /**
     * Get total quantity for an order.
     */
    public function getOrderTotalQuantity(int $orderId): int
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) FROM {$this->table} WHERE order_id = ?";
        return (int) $this->query($sql, [$orderId])->fetchColumn();
    }
}
