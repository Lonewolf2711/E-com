<?php
/**
 * Wishlist Model
 * ──────────────
 * Manages user wishlists (saved products).
 * Table: wishlists
 */

class Wishlist extends Model
{
    protected string $table = 'wishlists';

    /**
     * Get all wishlist items for a user.
     */
    public function getUserWishlist(int $userId): array
    {
        $sql = "SELECT w.*, p.name as product_name, p.slug as product_slug,
                       p.price, p.sale_price, p.image, p.stock, p.status,
                       c.name as category_name
                FROM wishlists w
                JOIN products p ON w.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
        return $this->query($sql, [$userId])->fetchAll();
    }

    /**
     * Toggle product in wishlist (add if not exists, remove if exists).
     */
    public function toggle(int $userId, int $productId): array
    {
        $existing = $this->findWhere('user_id = ? AND product_id = ?', [$userId, $productId]);

        if ($existing) {
            $this->delete($existing['id']);
            return ['action' => 'removed', 'message' => 'Removed from wishlist'];
        }

        $this->create([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);
        return ['action' => 'added', 'message' => 'Added to wishlist'];
    }

    /**
     * Check if product is in user's wishlist.
     */
    public function isInWishlist(int $userId, int $productId): bool
    {
        return $this->findWhere('user_id = ? AND product_id = ?', [$userId, $productId]) !== false;
    }

    /**
     * Get wishlist count for a user.
     */
    public function getUserCount(int $userId): int
    {
        return $this->count('user_id = ?', [$userId]);
    }

    /**
     * Remove a product from wishlist.
     */
    public function removeFromWishlist(int $userId, int $productId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        return $this->query($sql, [$userId, $productId])->rowCount() > 0;
    }
}
