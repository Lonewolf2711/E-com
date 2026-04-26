<?php
/**
 * Frontend Product Controller
 * ───────────────────────────
 * Handles single product page and product reviews.
 */

class FrontendProductController extends Controller
{
    /**
     * Display single product page.
     */
    public function show(string $slug): void
    {
        $productModel = new Product();
        $product = $productModel->findBySlug($slug);

        if (!$product) {
            $this->abort(404);
            return;
        }

        // Get product images
        $imageModel = new ProductImage();
        $images = $imageModel->getByProduct($product['id']);

        // Get product attributes
        $attrModel = new ProductAttribute();
        $attributes = $attrModel->getByProduct($product['id']);

        // Get reviews
        $reviewModel = new Review();
        $reviews = $reviewModel->getByProduct($product['id']);
        $avgRating = $reviewModel->getAverageRating($product['id']);
        $ratingBreakdown = $reviewModel->getRatingBreakdown($product['id']);

        // Check if user already reviewed
        $hasReviewed = false;
        if (is_logged_in()) {
            $hasReviewed = $reviewModel->hasReviewed(Auth::id(), $product['id']);
        }

        // Check if in wishlist
        $inWishlist = false;
        if (is_logged_in()) {
            $wishModel = new Wishlist();
            $inWishlist = $wishModel->isInWishlist(Auth::id(), $product['id']);
        }

        // Related products
        $related = $productModel->getRelated($product['id'], $product['category_id'] ?? 0, 4);

        $this->render('frontend/product', [
            'page_title'       => $product['name'],
            'meta_description' => $product['meta_description'] ?? truncate($product['description'] ?? '', 160),
            'product'          => $product,
            'images'           => $images,
            'attributes'       => $attributes,
            'reviews'          => $reviews,
            'avg_rating'       => $avgRating,
            'rating_breakdown' => $ratingBreakdown,
            'has_reviewed'     => $hasReviewed,
            'in_wishlist'      => $inWishlist,
            'related'          => $related,
        ]);
    }

    /**
     * Submit a product review.
     */
    public function review(string $slug): void
    {
        $productModel = new Product();
        $product = $productModel->findBySlug($slug);

        if (!$product) {
            $this->abort(404);
            return;
        }

        $reviewModel = new Review();

        // Check if already reviewed
        if ($reviewModel->hasReviewed(Auth::id(), $product['id'])) {
            Session::flash('error', 'You have already reviewed this product.');
            $this->redirect(url('/product/' . $slug));
            return;
        }

        $rating = (int) $this->post('rating', 0);
        $comment = trim($this->post('comment', ''));

        if ($rating < 1 || $rating > 5) {
            Session::flash('error', 'Please select a rating between 1 and 5.');
            $this->redirect(url('/product/' . $slug));
            return;
        }

        $reviewModel->submitReview($product['id'], Auth::id(), $rating, $comment);
        Session::flash('success', 'Thank you! Your review has been submitted and is pending moderation.');
        $this->redirect(url('/product/' . $slug));
    }
}
