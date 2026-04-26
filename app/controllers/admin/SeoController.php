<?php
/**
 * Admin SEO Controller
 * ────────────────────
 * Manages SEO metadata for products, categories, and pages.
 */

class AdminSeoController extends Controller
{
    private SeoMeta $seoModel;

    public function __construct()
    {
        $this->seoModel = new SeoMeta();
    }

    /**
     * List all SEO entries + forms for products/categories.
     */
    public function index(): void
    {
        $entries = $this->seoModel->getAdminList();

        // Get products and categories for adding new SEO
        $productModel = new Product();
        $categoryModel = new Category();
        $products = $productModel->findAll('name', 'ASC');
        $categories = $categoryModel->getAllFlat();

        $this->render('admin/seo/index', [
            'page_title' => 'SEO Management',
            'entries'    => $entries,
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Update/create SEO meta for a page type and ID.
     */
    public function update(string $type, int $id): void
    {
        $metaTitle = $this->post('meta_title', '');
        $metaDescription = $this->post('meta_description', '');
        $metaKeywords = $this->post('meta_keywords', '');
        $ogImage = $this->post('og_image', '');

        $data = [
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords'    => $metaKeywords,
            'og_image'         => $ogImage,
        ];

        if ($this->seoModel->setMeta($type, $id, $data)) {
            $this->redirect('/admin/seo', 'SEO meta updated successfully.', 'success');
        } else {
            $this->redirect('/admin/seo', 'Failed to update SEO meta.', 'error');
        }
    }
}
