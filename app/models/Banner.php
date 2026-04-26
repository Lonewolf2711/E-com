<?php
/**
 * Banner Model
 * ────────────
 * Manages homepage banners (hero slider, side blocks).
 */

class Banner extends Model
{
    protected string $table = 'banners';

    /**
     * Get all active banners for a given position, ordered by sort_order.
     *
     * @param string $position  hero|side_top|side_bottom
     * @return array
     */
    public function getByPosition(string $position): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE position = ? AND status = 'active'
                ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$position]);
        return $stmt->fetchAll();
    }

    /**
     * Get all banners grouped by position.
     *
     * @return array ['hero' => [...], 'side_top' => [...], 'side_bottom' => [...]]
     */
    public function getAllGroupedByPosition(): array
    {
        $all = $this->findAll('sort_order', 'ASC');
        $grouped = [
            'hero'        => [],
            'side_top'    => [],
            'side_bottom' => [],
        ];
        foreach ($all as $banner) {
            $pos = $banner['position'] ?? 'hero';
            $grouped[$pos][] = $banner;
        }
        return $grouped;
    }

    /**
     * Get all banners for admin listing.
     *
     * @return array
     */
    public function getAllAdmin(): array
    {
        return $this->findAll('sort_order', 'ASC');
    }
}
