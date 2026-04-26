<?php
/**
 * Cart Model
 * ──────────
 * Manages shopping carts (supports both logged-in users and guest sessions).
 * Table: carts
 */

class Cart extends Model
{
    protected string $table = 'carts';

    /**
     * Get or create a cart for the current user/session.
     */
    public function getOrCreate(): array
    {
        $userId = Auth::id();
        $sessionId = session_id();

        // Try to find existing cart
        if ($userId) {
            $cart = $this->findWhere('user_id = ?', [$userId]);
        } else {
            $cart = $this->findWhere('session_id = ?', [$sessionId]);
        }

        if ($cart) {
            return $cart;
        }

        // Create new cart
        $data = ['session_id' => $sessionId];
        if ($userId) {
            $data['user_id'] = $userId;
        }
        $cartId = $this->create($data);

        return $this->find($cartId);
    }

    /**
     * Merge guest cart into user cart after login.
     */
    public function mergeGuestCart(int $userId, string $sessionId): void
    {
        $db = Database::getInstance();

        // Find guest cart
        $guestCart = $this->findWhere('session_id = ? AND user_id IS NULL', [$sessionId]);
        if (!$guestCart) return;

        // Find or create user cart
        $userCart = $this->findWhere('user_id = ?', [$userId]);
        if (!$userCart) {
            $userCartId = $this->create(['user_id' => $userId, 'session_id' => $sessionId]);
            $userCart = $this->find($userCartId);
        }

        // Move guest cart items to user cart
        $db->prepare("UPDATE cart_items SET cart_id = ? WHERE cart_id = ?")->execute([$userCart['id'], $guestCart['id']]);

        // Delete guest cart
        $this->delete($guestCart['id']);
    }

    /**
     * Get cart with all items and product details.
     */
    public function getCartWithItems(): array
    {
        $cart = $this->getOrCreate();
        $cartItem = new CartItem();
        $items = $cartItem->getByCart($cart['id']);

        $subtotal = 0;
        foreach ($items as &$item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $item['line_total'] = $lineTotal;
            $subtotal += $lineTotal;
        }

        return [
            'cart'     => $cart,
            'items'    => $items,
            'count'    => count($items),
            'subtotal' => $subtotal,
        ];
    }

    /**
     * Clear all items from current cart.
     */
    public function clearCart(): void
    {
        $cart = $this->getOrCreate();
        $this->query("DELETE FROM cart_items WHERE cart_id = ?", [$cart['id']]);
    }
}
