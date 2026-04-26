<?php
/**
 * Frontend Shop Controller
 * ────────────────────────
 * Handles the shop page with filters, pagination, and search.
 */

class FrontendShopController extends Controller
{
    /**
     * Display shop page with filters.
     */
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $page = (int) ($this->get('page') ?: 1);
        $filters = [
            'category_id'   => $this->get('category') ?: '',
            'category_slug' => $this->get('cat') ?: '',
            'min_price'     => $this->get('min_price') ?: '',
            'max_price'     => $this->get('max_price') ?: '',
            'sort'          => $this->get('sort') ?: 'newest',
            'search'        => $this->get('q') ?: '',
        ];

        $products = $productModel->getFiltered($filters, $page, 12);
        $categories = $categoryModel->getCategoriesWithCount();

        $this->render('frontend/shop', [
            'page_title' => 'Industrial Spare Parts',
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * Search results page.
     */
    public function search(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $query = trim($this->get('q') ?: '');
        $page = (int) ($this->get('page') ?: 1);

        $filters = [
            'search'        => $query,
            'category_slug' => $this->get('cat') ?: '',
            'sort'          => $this->get('sort') ?: 'newest',
        ];

        $products = $productModel->getFiltered($filters, $page, 12);
        $categories = $categoryModel->getCategoriesWithCount();

        $this->render('frontend/shop', [
            'page_title'  => $query ? "Search results for '{$query}'" : 'Search',
            'products'    => $products,
            'categories'  => $categories,
            'filters'     => $filters,
            'search_mode' => true,
        ]);
    }
}
