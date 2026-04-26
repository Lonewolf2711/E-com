<?php
/**
 * Admin Product Controller
 * ────────────────────────
 * Full CRUD for products: list, create, edit, delete.
 */

class AdminProductController extends Controller
{
    /**
     * List all products with search/filter.
     */
    public function index(): void
    {
        $productModel = new Product();
        $page = (int) ($this->get('page') ?: 1);
        $search = $this->get('q') ?: '';
        $status = $this->get('status') ?: '';
        $categoryId = $this->get('category') ?: '';

        $filters = [
            'search'      => $search,
            'status'      => $status,
            'category_id' => $categoryId,
            'sort'        => $this->get('sort') ?: 'newest',
        ];

        $products = $productModel->getAdminList($page, 15, $search, $status);

        $categoryModel = new Category();
        $categories = $categoryModel->getAllFlat();

        $this->render('admin/products/index', [
            'page_title'  => 'Products',
            'products'    => $products,
            'categories'  => $categories,
            'filters'     => $filters,
        ]);
    }

    /**
     * Show add product form.
     */
    public function addForm(): void
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllFlat();

        $this->render('admin/products/form', [
            'page_title'  => 'Add Product',
            'product'     => null,
            'categories'  => $categories,
            'images'      => [],
            'attributes'  => [],
        ]);
    }

    /**
     * Store new product.
     */
    public function store(): void
    {
        $data = [
            'name'              => trim($this->post('name', '')),
            'slug'              => slugify(trim($this->post('product_code', '')) . '-' . trim($this->post('name', ''))),
            'description'       => trim($this->post('description', '')),
            'short_description' => trim($this->post('short_description', '')),
            'sku'               => trim($this->post('sku', '')),
            'price'             => (float) $this->post('price', 0),
            'sale_price'        => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'stock'             => (int) $this->post('stock', 0),
            'category_id'       => (int) $this->post('category_id', 0),
            'product_code'      => trim($this->post('product_code', '')),
            'machine_name'      => trim($this->post('machine_name', '')),
            'machine_model'     => trim($this->post('machine_model', '')),
            'compatible_machines'=> trim($this->post('compatible_machines', '')),
            'status'            => $this->post('status', 'active'),
            'is_featured'       => $this->post('is_featured') ? 1 : 0,
            'meta_title'        => trim($this->post('meta_title', '')),
            'meta_description'  => trim($this->post('meta_description', '')),
        ];

        // Validate
        if (empty($data['name']) || empty($data['product_code']) || $data['price'] <= 0 || $data['category_id'] <= 0) {
            Session::flash('error', 'Product name, product code, price, and category are required.');
            $this->redirect(url('/admin/products/add'));
            return;
        }

        $productModel = new Product();
        if ($productModel->findWhere('product_code = ?', [$data['product_code']])) {
            Session::flash('error', 'Product code already exists.');
            $this->redirect(url('/admin/products/add'));
            return;
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'products');
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['path'];
            } else {
                Session::flash('error', $uploadResult['error']);
                $this->redirect(url('/admin/products/add'));
                return;
            }
        }

        $productModel = new Product();
        $productId = $productModel->create($data);

        // Handle gallery images
        if (!empty($_FILES['gallery'])) {
            $imageModel = new ProductImage();
            $files = $_FILES['gallery'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i])) {
                    $file = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $result = handleUpload($file, 'products');
                    if ($result['success']) {
                        $imageModel->create([
                            'product_id' => $productId,
                            'image_path' => $result['path'],
                            'sort_order' => $i,
                        ]);
                    }
                }
            }
        }

        // Handle attributes
        $attrNames = $this->post('attr_name') ?? [];
        $attrValues = $this->post('attr_value') ?? [];
        if (!empty($attrNames)) {
            $attrModel = new ProductAttribute();
            foreach ($attrNames as $idx => $attrName) {
                if (!empty($attrName) && !empty($attrValues[$idx])) {
                    $attrModel->create([
                        'product_id'      => $productId,
                        'attribute_name'  => trim($attrName),
                        'attribute_value' => trim($attrValues[$idx]),
                    ]);
                }
            }
        }

        Session::flash('success', 'Product created successfully.');
        $this->redirect(url('/admin/products'));
    }

    /**
     * Show edit product form.
     */
    public function editForm(int $id): void
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            Session::flash('error', 'Product not found.');
            $this->redirect(url('/admin/products'));
            return;
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAllFlat();

        $imageModel = new ProductImage();
        $images = $imageModel->getByProduct($id);

        $attrModel = new ProductAttribute();
        $attributes = $attrModel->getByProduct($id);

        $this->render('admin/products/form', [
            'page_title'  => 'Edit Product',
            'product'     => $product,
            'categories'  => $categories,
            'images'      => $images,
            'attributes'  => $attributes,
        ]);
    }

    /**
     * Update existing product.
     */
    public function update(int $id): void
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            Session::flash('error', 'Product not found.');
            $this->redirect(url('/admin/products'));
            return;
        }

        $data = [
            'name'              => trim($this->post('name', '')),
            'slug'              => slugify(trim($this->post('product_code', '')) . '-' . trim($this->post('name', ''))),
            'description'       => trim($this->post('description', '')),
            'short_description' => trim($this->post('short_description', '')),
            'sku'               => trim($this->post('sku', '')),
            'price'             => (float) $this->post('price', 0),
            'sale_price'        => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'stock'             => (int) $this->post('stock', 0),
            'category_id'       => (int) $this->post('category_id', 0),
            'product_code'      => trim($this->post('product_code', '')),
            'machine_name'      => trim($this->post('machine_name', '')),
            'machine_model'     => trim($this->post('machine_model', '')),
            'compatible_machines'=> trim($this->post('compatible_machines', '')),
            'status'            => $this->post('status', 'active'),
            'is_featured'       => $this->post('is_featured') ? 1 : 0,
            'meta_title'        => trim($this->post('meta_title', '')),
            'meta_description'  => trim($this->post('meta_description', '')),
        ];

        if (empty($data['name']) || empty($data['product_code']) || $data['price'] <= 0) {
            Session::flash('error', 'Product name, product code, and price are required.');
            $this->redirect(url('/admin/products/edit/' . $id));
            return;
        }

        if ($productModel->findWhere('product_code = ? AND id != ?', [$data['product_code'], $id])) {
            Session::flash('error', 'Product code already exists.');
            $this->redirect(url('/admin/products/edit/' . $id));
            return;
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = handleUpload($_FILES['image'], 'products');
            if ($uploadResult['success']) {
                // Delete old image
                if ($product['image']) {
                    deleteUpload($product['image']);
                }
                $data['image'] = $uploadResult['path'];
            }
        }

        $productModel->update($id, $data);

        // Handle gallery images
        if (!empty($_FILES['gallery'])) {
            $imageModel = new ProductImage();
            $files = $_FILES['gallery'];
            $existingCount = count($imageModel->getByProduct($id));
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i])) {
                    $file = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $result = handleUpload($file, 'products');
                    if ($result['success']) {
                        $imageModel->create([
                            'product_id' => $id,
                            'image_path' => $result['path'],
                            'sort_order' => $existingCount + $i,
                        ]);
                    }
                }
            }
        }

        // Update attributes
        $attrNames = $this->post('attr_name') ?? [];
        $attrValues = $this->post('attr_value') ?? [];
        $attrModel = new ProductAttribute();
        $attrModel->deleteByProduct($id);
        foreach ($attrNames as $idx => $attrName) {
            if (!empty($attrName) && !empty($attrValues[$idx])) {
                $attrModel->create([
                    'product_id'      => $id,
                    'attribute_name'  => trim($attrName),
                    'attribute_value' => trim($attrValues[$idx]),
                ]);
            }
        }

        Session::flash('success', 'Product updated successfully.');
        $this->redirect(url('/admin/products'));
    }

    /**
     * Delete a product.
     */
    public function delete(int $id): void
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if ($product) {
            if ($product['image']) {
                deleteUpload($product['image']);
            }
            // Delete gallery images
            $imageModel = new ProductImage();
            $images = $imageModel->getByProduct($id);
            foreach ($images as $img) {
                deleteUpload($img['image_path']);
                $imageModel->delete($img['id']);
            }
            // Delete attributes
            $attrModel = new ProductAttribute();
            $attrModel->deleteByProduct($id);

            $productModel->delete($id);
            Session::flash('success', 'Product deleted.');
        } else {
            Session::flash('error', 'Product not found.');
        }

        $this->redirect(url('/admin/products'));
    }
}
