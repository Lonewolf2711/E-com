<?php
/**
 * Cart Item Model
 * ───────────────
 * Manages individual items in a shopping cart.
 * Table: cart_items
 */

class CartItem extends Model
{
    protected string $table = 'cart_items';

    /**
     * Get all items for a cart with product details.
     */
    public function getByCart(int $cartId): array
    {
        $sql = "SELECT ci.*, p.name as product_name, p.slug as product_slug,
                       p.image as product_image, p.stock as product_stock,
                       p.product_code as product_code, p.sku as product_sku,
                       p.sale_price, p.price as current_price,
                       COALESCE(p.sale_price, p.price) as effective_price
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = ?
                ORDER BY ci.created_at DESC";
        return $this->query($sql, [$cartId])->fetchAll();
    }

    /**
     * Add a product to the cart (or update quantity if exists).
     */
    public function addToCart(int $cartId, int $productId, int $quantity, float $price): int|bool
    {
        // Check if product already in cart
        $existing = $this->findWhere('cart_id = ? AND product_id = ?', [$cartId, $productId]);

        if ($existing) {
            // Update quantity
            $newQty = $existing['quantity'] + $quantity;
            return $this->update($existing['id'], ['quantity' => $newQty, 'price' => $price]);
        }

        // Add new item
        return $this->create([
            'cart_id'    => $cartId,
            'product_id' => $productId,
            'quantity'   => $quantity,
            'price'      => $price,
        ]);
    }

    /**
     * Update item quantity.
     */
    public function updateQuantity(int $itemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->delete($itemId);
        }
        return $this->update($itemId, ['quantity' => $quantity]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $cartId, int $productId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE cart_id = ? AND product_id = ?";
        return $this->query($sql, [$cartId, $productId])->rowCount() > 0;
    }

    /**
     * Count items in a cart.
     */
    public function countItems(int $cartId): int
    {
        return $this->count('cart_id = ?', [$cartId]);
    }
}
