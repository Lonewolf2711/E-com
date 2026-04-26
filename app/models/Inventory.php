<?php
/**
 * Inventory Model
 * ───────────────
 * Manages inventory logs and stock tracking.
 * Table: inventory_logs
 */

class Inventory extends Model
{
    protected string $table = 'inventory_logs';

    /**
     * Log a stock change and update product stock.
     */
    public function logChange(int $productId, int $amount, string $type, string $note = '', ?int $createdBy = null): int
    {
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            // Get current stock
            $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $currentStock = (int) $stmt->fetchColumn();

            $newStock = $currentStock + $amount;
            if ($newStock < 0) $newStock = 0;

            // Update product stock
            $db->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$newStock, $productId]);

            // Create log entry
            $logId = $this->create([
                'product_id'    => $productId,
                'change_amount' => $amount,
                'stock_before'  => $currentStock,
                'stock_after'   => $newStock,
                'type'          => $type,
                'note'          => $note,
                'created_by'    => $createdBy,
            ]);

            $db->commit();
            return $logId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Deduct stock on sale.
     */
    public function deductOnSale(int $productId, int $quantity, int $orderId): int
    {
        return $this->logChange(
            $productId,
            -$quantity,
            'sale',
            "Deducted for order #{$orderId}"
        );
    }

    /**
     * Restore stock on order cancellation/return.
     */
    public function restoreOnReturn(int $productId, int $quantity, int $orderId, ?int $adminId = null): int
    {
        return $this->logChange(
            $productId,
            $quantity,
            'return',
            "Restored from order #{$orderId}",
            $adminId
        );
    }

    /**
     * Get inventory logs for a product.
     */
    public function getProductLogs(int $productId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $total = $this->count('product_id = ?', [$productId]);

        $sql = "SELECT il.*, u.name as admin_name
                FROM inventory_logs il
                LEFT JOIN users u ON il.created_by = u.id
                WHERE il.product_id = ?
                ORDER BY il.created_at DESC
                LIMIT ? OFFSET ?";
        $data = $this->query($sql, [$productId, $perPage, $offset])->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'pages'        => (int) ceil($total / $perPage),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Get all recent inventory logs (admin overview).
     */
    public function getRecentLogs(int $limit = 20): array
    {
        $sql = "SELECT il.*, p.name as product_name, p.sku, u.name as admin_name
                FROM inventory_logs il
                JOIN products p ON il.product_id = p.id
                LEFT JOIN users u ON il.created_by = u.id
                ORDER BY il.created_at DESC
                LIMIT ?";
        return $this->query($sql, [$limit])->fetchAll();
    }
}
