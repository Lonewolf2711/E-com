<?php
/**
 * Category Model
 * ──────────────
 * Manages product categories with parent-child hierarchy.
 * Table: categories
 */

class Category extends Model
{
    protected string $table = 'categories';

    /**
     * Find category by slug.
     */
    public function findBySlug(string $slug): array|false
    {
        return $this->findWhere('slug = ?', [$slug]);
    }

    /**
     * Get all active categories.
     */
    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'name', 'ASC');
    }

    /**
     * Get all parent categories (no parent_id).
     */
    public function getParentCategories(): array
    {
        return $this->where("parent_id IS NULL AND status = 'active'", [], 'name', 'ASC');
    }

    /**
     * Get sub-categories for a parent.
     */
    public function getSubCategories(int $parentId): array
    {
        return $this->where('parent_id = ?', [$parentId], 'name', 'ASC');
    }

    /**
     * Get categories with product count.
     */
    public function getCategoriesWithCount(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                WHERE c.status = 'active'
                GROUP BY c.id
                ORDER BY c.name ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Get category tree (parents with their children).
     */
    public function getCategoryTree(): array
    {
        $parents = $this->getParentCategories();
        foreach ($parents as &$parent) {
            $parent['children'] = $this->getSubCategories($parent['id']);
        }
        return $parents;
    }

    /**
     * Check if slug already exists (for validation).
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $result = $this->findWhere('slug = ? AND id != ?', [$slug, $excludeId]);
        } else {
            $result = $this->findWhere('slug = ?', [$slug]);
        }
        return $result !== false;
    }

    /**
     * Get all categories for admin (paginated).
     */
    public function getAllPaginated(int $page = 1, int $perPage = 15): array
    {
        return $this->paginate($page, $perPage, 'name', 'ASC');
    }

    /**
     * Get all categories in a flat list.
     */
    public function getAllFlat(): array
    {
        return $this->findAll('name', 'ASC');
    }
}
