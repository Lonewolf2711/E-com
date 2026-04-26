<?php
/**
 * Frontend Home Controller
 * ────────────────────────
 * Handles the public homepage.
 */

class FrontendHomeController extends Controller
{
    /**
     * Display the homepage.
     * Shows featured products, active categories, and hero banner.
     */
    public function index(): void
    {
        $data = [];

        try {
            $db = Database::getInstance();

            // Get featured products (is_featured = 1, active status)
            $stmt = $db->prepare(
                "SELECT p.*, c.name as category_name
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.status = 'active' AND p.is_featured = 1
                 ORDER BY p.created_at DESC
                 LIMIT 8"
            );
            $stmt->execute();
            $data['featured_products'] = $stmt->fetchAll();

            // Get all active products for "Our Products" section
            $stmt = $db->prepare(
                "SELECT p.*, c.name as category_name
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.status = 'active'
                 ORDER BY p.created_at DESC
                 LIMIT 8"
            );
            $stmt->execute();
            $data['all_products'] = $stmt->fetchAll();

            // Get active categories with product count
            $stmt = $db->prepare(
                "SELECT c.*, COUNT(p.id) as product_count
                 FROM categories c
                 LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                 WHERE c.status = 'active' AND c.parent_id IS NULL
                 GROUP BY c.id
                 ORDER BY c.name ASC"
            );
            $stmt->execute();
            $data['categories'] = $stmt->fetchAll();

            // Store name from settings
            $data['store_name'] = get_setting('general_store_name', 'Electro Store');
            $data['tagline'] = get_setting('general_tagline', 'Your One-Stop Electronics Shop');

            // Get homepage banners
            $bannerModel = new Banner();
            $data['hero_banners']  = $bannerModel->getByPosition('hero');
            $data['side_top']      = $bannerModel->getByPosition('side_top');
            $data['side_bottom']   = $bannerModel->getByPosition('side_bottom');

        } catch (Exception $e) {
            // If database isn't set up yet, show empty data
            $data['featured_products'] = [];
            $data['all_products'] = [];
            $data['categories'] = [];
            $data['store_name'] = 'Electro Store';
            $data['tagline'] = 'Your One-Stop Electronics Shop';
            $data['hero_banners'] = [];
            $data['side_top'] = [];
            $data['side_bottom'] = [];
        }

        $data['page_title'] = 'Home';

        $this->render('frontend/home', $data);
    }
}
