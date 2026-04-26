<?php
/**
 * Product Image Model
 * ───────────────────
 * Manages product gallery images.
 * Table: product_images
 */

class ProductImage extends Model
{
    protected string $table = 'product_images';

    /**
     * Get all images for a product, ordered by sort_order.
     */
    public function getByProduct(int $productId): array
    {
        return $this->where('product_id = ?', [$productId], 'sort_order', 'ASC');
    }

    /**
     * Add an image to a product.
     */
    public function addImage(int $productId, string $imagePath, int $sortOrder = 0): int
    {
        return $this->create([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Delete all images for a product.
     */
    public function deleteByProduct(int $productId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE product_id = ?";
        return $this->query($sql, [$productId])->rowCount() >= 0;
    }

    /**
     * Update sort order for an image.
     */
    public function updateSortOrder(int $imageId, int $sortOrder): bool
    {
        return $this->update($imageId, ['sort_order' => $sortOrder]);
    }
}
