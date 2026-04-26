<?php
/**
 * Review Model
 * ────────────
 * Manages product reviews with moderation.
 * Table: reviews
 */

class Review extends Model
{
    protected string $table = 'reviews';

    /**
     * Get approved reviews for a product.
     */
    public function getByProduct(int $productId): array
    {
        $sql = "SELECT r.*, u.name as user_name
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ? AND r.status = 'approved'
                ORDER BY r.created_at DESC";
        return $this->query($sql, [$productId])->fetchAll();
    }

    /**
     * Get average rating for a product.
     */
    public function getAverageRating(int $productId): float
    {
        $sql = "SELECT COALESCE(AVG(rating), 0) FROM {$this->table}
                WHERE product_id = ? AND status = 'approved'";
        return round((float) $this->query($sql, [$productId])->fetchColumn(), 1);
    }

    /**
     * Get rating breakdown (how many 5-star, 4-star, etc.).
     */
    public function getRatingBreakdown(int $productId): array
    {
        $sql = "SELECT rating, COUNT(*) as count FROM {$this->table}
                WHERE product_id = ? AND status = 'approved'
                GROUP BY rating ORDER BY rating DESC";
        $results = $this->query($sql, [$productId])->fetchAll();

        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($results as $row) {
            $breakdown[$row['rating']] = $row['count'];
        }
        return $breakdown;
    }

    /**
     * Check if user has already reviewed a product.
     */
    public function hasReviewed(int $userId, int $productId): bool
    {
        return $this->findWhere('user_id = ? AND product_id = ?', [$userId, $productId]) !== false;
    }

    /**
     * Submit a review (pending moderation).
     */
    public function submitReview(int $productId, int $userId, int $rating, string $comment): int
    {
        return $this->create([
            'product_id' => $productId,
            'user_id'    => $userId,
            'rating'     => max(1, min(5, $rating)),
            'comment'    => $comment,
            'status'     => 'pending',
        ]);
    }

    /**
     * Get pending reviews for admin moderation.
     */
    public function getPendingReviews(int $page = 1, int $perPage = 15): array
    {
        $where = "r.status = 'pending'";
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM reviews r WHERE {$where}";
        $total = (int) $this->query($countSql)->fetchColumn();

        $sql = "SELECT r.*, u.name as user_name, p.name as product_name
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE {$where}
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        $data = $this->query($sql, [$perPage, $offset])->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'pages'        => (int) ceil($total / $perPage),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Approve or reject a review.
     */
    public function moderate(int $reviewId, string $status): bool
    {
        return $this->update($reviewId, ['status' => $status]);
    }
}
