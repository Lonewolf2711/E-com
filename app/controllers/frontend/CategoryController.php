<?php
/**
 * Frontend Category Controller
 * ────────────────────────────
 * Displays products within a specific category.
 */

class FrontendCategoryController extends Controller
{
    /**
     * Show products in a category.
     */
    public function show(string $slug): void
    {
        $categoryModel = new Category();
        $category = $categoryModel->findBySlug($slug);

        if (!$category) {
            $this->abort(404);
            return;
        }

        $productModel = new Product();
        $page = (int) ($this->get('page') ?: 1);

        $filters = [
            'category_slug' => $slug,
            'sort'          => $this->get('sort') ?: 'newest',
            'min_price'     => $this->get('min_price') ?: '',
            'max_price'     => $this->get('max_price') ?: '',
        ];

        $products = $productModel->getFiltered($filters, $page, 12);
        $categories = $categoryModel->getCategoriesWithCount();

        // Get sub-categories
        $subCategories = $categoryModel->getSubCategories($category['id']);

        $this->render('frontend/shop', [
            'page_title'      => $category['name'] . ' Spare Parts',
            'products'        => $products,
            'categories'      => $categories,
            'sub_categories'  => $subCategories,
            'current_category'=> $category,
            'filters'         => $filters,
            'category_mode'   => true,
        ]);
    }
}
