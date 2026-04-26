<?php
/**
 * Frontend Wishlist Controller
 * ────────────────────────────
 */

class FrontendWishlistController extends Controller
{
    /**
     * Display wishlist.
     */
    public function index(): void
    {
        $wishModel = new Wishlist();
        $items = $wishModel->getUserWishlist(Auth::id());

        $this->render('frontend/wishlist', [
            'page_title' => 'My Wishlist',
            'items'      => $items,
        ]);
    }

    /**
     * Toggle wishlist (add/remove).
     */
    public function toggle(): void
    {
        Middleware::verifyCsrf();

        $productId = (int) $this->post('product_id', 0);
        $wishModel = new Wishlist();
        $result = $wishModel->toggle(Auth::id(), $productId);

        Session::flash('success', $result['message']);

        // Redirect back
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/wishlist');
        $this->redirect($referer);
    }
}
