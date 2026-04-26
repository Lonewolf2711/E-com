<?php
/**
 * Admin Category Controller
 * ─────────────────────────
 * CRUD for categories with parent-child management.
 */

class AdminCategoryController extends Controller
{
    /**
     * List all categories as a tree.
     */
    public function index(): void
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getCategoryTree();
        $flatCategories = $categoryModel->getAllFlat();

        $this->render('admin/categories/index', [
            'page_title'      => 'Categories',
            'categories'      => $categories,
            'flat_categories'  => $flatCategories,
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(): void
    {
        $name = trim($this->post('name', ''));
        $parentId = $this->post('parent_id') ? (int) $this->post('parent_id') : null;
        $description = trim($this->post('description', ''));

        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            $this->redirect(url('/admin/categories'));
            return;
        }

        $slug = slugify($name);

        // Check for duplicate slug
        $categoryModel = new Category();
        $existing = $categoryModel->findBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'parent_id'   => $parentId,
            'description' => $description,
            'status'      => $this->post('status', 'active'),
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'categories');
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['path'];
            }
        }

        $categoryModel->create($data);

        Session::flash('success', 'Category created successfully.');
        $this->redirect(url('/admin/categories'));
    }

    /**
     * Update an existing category.
     */
    public function update(int $id): void
    {
        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('/admin/categories'));
            return;
        }

        $name = trim($this->post('name', ''));
        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            $this->redirect(url('/admin/categories'));
            return;
        }

        $data = [
            'name'        => $name,
            'slug'        => slugify($name),
            'parent_id'   => $this->post('parent_id') ? (int) $this->post('parent_id') : null,
            'description' => trim($this->post('description', '')),
            'status'      => $this->post('status', 'active'),
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'categories');
            if ($uploadResult['success']) {
                if ($category['image']) {
                    deleteUpload($category['image']);
                }
                $data['image'] = $uploadResult['path'];
            }
        }

        $categoryModel->update($id, $data);

        Session::flash('success', 'Category updated successfully.');
        $this->redirect(url('/admin/categories'));
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): void
    {
        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::flash('error', 'Category not found.');
            $this->redirect(url('/admin/categories'));
            return;
        }

        // Check for sub-categories
        $children = $categoryModel->getSubCategories($id);
        if (!empty($children)) {
            Session::flash('error', 'Cannot delete category with sub-categories. Remove them first.');
            $this->redirect(url('/admin/categories'));
            return;
        }

        // Check for products in category
        $productModel = new Product();
        $productCount = $productModel->countByCategory($id);
        if ($productCount > 0) {
            Session::flash('error', "Cannot delete category with {$productCount} product(s). Reassign them first.");
            $this->redirect(url('/admin/categories'));
            return;
        }

        if ($category['image']) {
            deleteUpload($category['image']);
        }
        $categoryModel->delete($id);

        Session::flash('success', 'Category deleted successfully.');
        $this->redirect(url('/admin/categories'));
    }
}
