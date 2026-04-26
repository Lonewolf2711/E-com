<?php
/**
 * Product Attribute Model
 * ───────────────────────
 * Manages dynamic product attributes (Color, Size, RAM, etc.).
 * Table: product_attributes
 */

class ProductAttribute extends Model
{
    protected string $table = 'product_attributes';

    /**
     * Get all attributes for a product.
     */
    public function getByProduct(int $productId): array
    {
        return $this->where('product_id = ?', [$productId], 'id', 'ASC');
    }

    /**
     * Set attributes for a product (delete existing, insert new).
     */
    public function setAttributes(int $productId, array $attributes): void
    {
        // Delete existing
        $this->query("DELETE FROM {$this->table} WHERE product_id = ?", [$productId]);

        // Insert new
        foreach ($attributes as $attr) {
            if (!empty($attr['name']) && !empty($attr['value'])) {
                $this->create([
                    'product_id'      => $productId,
                    'attribute_name'  => $attr['name'],
                    'attribute_value' => $attr['value'],
                ]);
            }
        }
    }

    /**
     * Delete all attributes for a product.
     */
    public function deleteByProduct(int $productId): bool
    {
        return $this->query("DELETE FROM {$this->table} WHERE product_id = ?", [$productId])->rowCount() >= 0;
    }
}
