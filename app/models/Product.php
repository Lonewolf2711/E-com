<?php
/**
 * Product Model
 * ─────────────
 * Handles product CRUD, search, filtering, and featured products.
 * Table: products
 */

class Product extends Model
{
    protected string $table = 'products';

    /**
     * Find product by slug (for frontend single-product page).
     */
    public function findBySlug(string $slug): array|false
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.status = 'active'
                LIMIT 1";
        return $this->query($sql, [$slug])->fetch();
    }

    /**
     * Find product by SKU.
     */
    public function findBySku(string $sku): array|false
    {
        return $this->findWhere('sku = ?', [$sku]);
    }

    /**
     * Get featured products.
     */
    public function getFeatured(int $limit = 8): array
    {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        return $this->query($sql, [$limit])->fetchAll();
    }

    /**
     * Get products with filters and pagination (for shop page).
     */
    public function getFiltered(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = ["p.status = 'active'"];
        $params = [];

        // Category filter
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }

        // Category slug filter
        if (!empty($filters['category_slug'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category_slug'];
        }

        // Price range
        if (!empty($filters['min_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.price) >= ?';
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.price) <= ?';
            $params[] = $filters['max_price'];
        }

        // Search query
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ? OR p.product_code LIKE ? OR p.machine_name LIKE ? OR p.compatible_machines LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // Sort
        $orderBy = match ($filters['sort'] ?? 'newest') {
            'price_low'  => 'COALESCE(p.sale_price, p.price) ASC',
            'price_high' => 'COALESCE(p.sale_price, p.price) DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            'popular'    => 'p.is_featured DESC, p.created_at DESC',
            default      => 'p.created_at DESC',
        };

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // Count total
        $countSql = "SELECT COUNT(*) FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        // Fetch products
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN reviews r ON p.id = r.product_id AND r.status = 'approved'
                WHERE {$whereClause}
                GROUP BY p.id
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";

        $fetchParams = array_merge($params, [$perPage, $offset]);
        $data = $this->query($sql, $fetchParams)->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'pages'        => (int) ceil($total / $perPage),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Get related products (same category, excluding current).
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.category_id = ? AND p.id != ?
                ORDER BY RAND()
                LIMIT ?";
        return $this->query($sql, [$categoryId, $productId, $limit])->fetchAll();
    }

    /**
     * Get all products for admin (paginated with search).
     */
    public function getAdminList(int $page = 1, int $perPage = 15, string $search = '', string $status = ''): array
    {
        $where = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.product_code LIKE ? OR p.machine_name LIKE ?)';
            $like = "%{$search}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($status) {
            $where[] = 'p.status = ?';
            $params[] = $status;
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM products p WHERE {$whereClause}";
        $total = (int) $this->query($countSql, $params)->fetchColumn();

        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
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
     * Get low stock products.
     */
    public function getLowStock(int $page = 1, int $perPage = 15): array
    {
        $where = "stock <= low_stock_threshold AND status = 'active'";
        return $this->paginate($page, $perPage, 'stock', 'ASC', $where);
    }

    /**
     * Update product stock.
     */
    public function updateStock(int $id, int $amount): bool
    {
        $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
        return $this->query($sql, [$amount, $id])->rowCount() > 0;
    }

    /**
     * Check slug uniqueness.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            return $this->findWhere('slug = ? AND id != ?', [$slug, $excludeId]) !== false;
        }
        return $this->findWhere('slug = ?', [$slug]) !== false;
    }

    /**
     * Get product stats for dashboard.
     */
    public function getStats(): array
    {
        $total = $this->count("status = 'active'");
        $lowStock = $this->count("stock <= low_stock_threshold AND status = 'active'");
        $outOfStock = $this->count("stock = 0 AND status = 'active'");
        $featured = $this->count("is_featured = 1 AND status = 'active'");

        return [
            'total'        => $total,
            'low_stock'    => $lowStock,
            'out_of_stock' => $outOfStock,
            'featured'     => $featured,
        ];
    }

    /**
     * Get best selling products.
     */
    public function getBestSellers(int $limit = 10): array
    {
        $sql = "SELECT p.*, c.name as category_name,
                       COALESCE(SUM(oi.quantity), 0) as total_sold,
                       COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
                WHERE p.status = 'active'
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT ?";
        return $this->query($sql, [$limit])->fetchAll();
    }

    /**
     * Count products by category.
     */
    public function countByCategory(int $categoryId): int
    {
        return $this->count('category_id = ?', [$categoryId]);
    }
}
