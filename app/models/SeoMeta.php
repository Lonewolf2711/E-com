<?php
/**
 * SEO Meta Model
 * ──────────────
 * Manages SEO metadata for pages, products, and categories.
 * Table: seo_meta
 */

class SeoMeta extends Model
{
    protected string $table = 'seo_meta';

    /**
     * Get SEO meta for a specific page/entity.
     */
    public function getMeta(string $pageType, int $pageId): array|false
    {
        return $this->findWhere('page_type = ? AND page_id = ?', [$pageType, $pageId]);
    }

    /**
     * Set SEO meta for a page (upsert).
     */
    public function setMeta(string $pageType, int $pageId, array $metaData): bool
    {
        $existing = $this->getMeta($pageType, $pageId);

        $data = array_merge([
            'page_type' => $pageType,
            'page_id'   => $pageId,
        ], $metaData);

        if ($existing) {
            return $this->update($existing['id'], $metaData);
        }

        return (bool) $this->create($data);
    }

    /**
     * Get all SEO entries by type.
     */
    public function getByType(string $pageType): array
    {
        return $this->where('page_type = ?', [$pageType], 'page_id', 'ASC');
    }

    /**
     * Get all SEO entries with page titles (admin listing).
     */
    public function getAdminList(): array
    {
        $sql = "SELECT sm.*,
                       CASE
                           WHEN sm.page_type = 'product' THEN (SELECT name FROM products WHERE id = sm.page_id)
                           WHEN sm.page_type = 'category' THEN (SELECT name FROM categories WHERE id = sm.page_id)
                           ELSE CONCAT(sm.page_type, ' #', sm.page_id)
                       END as page_title
                FROM seo_meta sm
                ORDER BY sm.page_type ASC, sm.page_id ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Delete SEO meta for a page.
     */
    public function deleteMeta(string $pageType, int $pageId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE page_type = ? AND page_id = ?";
        return $this->query($sql, [$pageType, $pageId])->rowCount() > 0;
    }
}
