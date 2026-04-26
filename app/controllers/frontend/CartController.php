<?php
/**
 * Frontend Cart Controller
 * ────────────────────────
 * Cart page, add/update/remove items.
 */

class FrontendCartController extends Controller
{
    /**
     * Display cart page.
     */
    public function index(): void
    {
        $cartModel = new Cart();
        $cartData = $cartModel->getCartWithItems();

        $this->render('frontend/cart', [
            'page_title' => 'Shopping Cart',
            'cart'        => $cartData,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function add(): void
    {
        Middleware::verifyCsrf();

        $productId = (int) $this->post('product_id', 0);
        $quantity = (int) $this->post('quantity', 1);

        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product || $product['status'] !== 'active') {
            Session::flash('error', 'Product not found or unavailable.');
            $this->redirect(url('/shop'));
            return;
        }

        if ($quantity > $product['stock']) {
            Session::flash('error', 'Only ' . $product['stock'] . ' items available in stock.');
            $this->redirect(url('/product/' . $product['slug']));
            return;
        }

        $effectivePrice = $product['sale_price'] ?: $product['price'];

        $cartModel = new Cart();
        $cart = $cartModel->getOrCreate();

        $cartItemModel = new CartItem();
        $cartItemModel->addToCart($cart['id'], $productId, $quantity, $effectivePrice);

        Session::flash('success', e($product['name']) . ' has been added to your cart.');
        $this->redirect(url('/cart'));
    }

    /**
     * Update cart item quantity.
     */
    public function update(): void
    {
        Middleware::verifyCsrf();

        $itemId = (int) $this->post('item_id', 0);
        $quantity = (int) $this->post('quantity', 0);

        $cartItemModel = new CartItem();

        if ($quantity <= 0) {
            $cartItemModel->delete($itemId);
            Session::flash('success', 'Item removed from cart.');
        } else {
            $cartItemModel->updateQuantity($itemId, $quantity);
            Session::flash('success', 'Cart updated.');
        }

        $this->redirect(url('/cart'));
    }

    /**
     * Remove item from cart.
     */
    public function remove(): void
    {
        Middleware::verifyCsrf();

        $itemId = (int) $this->post('item_id', 0);
        $cartItemModel = new CartItem();
        $cartItemModel->delete($itemId);

        Session::flash('success', 'Item removed from cart.');
        $this->redirect(url('/cart'));
    }
}
