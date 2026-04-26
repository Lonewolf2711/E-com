<?php
/**
 * Admin Inventory Controller
 * ──────────────────────────
 * Manages product stock, low stock alerts, and quick adjustments.
 */

class AdminInventoryController extends Controller
{
    private Product $productModel;
    private Inventory $inventoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->inventoryModel = new Inventory();
    }

    /**
     * View full inventory list with search.
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $search = $this->get('search', '');
        
        // Pass empty status to get all statuses except deleted maybe.
        // Product getAdminList handles empty status as 'all'.
        $products = $this->productModel->getAdminList($page, 20, $search, 'active');

        $this->render('admin/inventory/index', [
            'page_title' => 'Inventory Management',
            'products'   => $products,
            'search'     => $search,
        ]);
    }

    /**
     * View low stock alerts.
     */
    public function lowStock(): void
    {
        $page = (int) $this->get('page', 1);
        $products = $this->productModel->getLowStock($page, 20);

        $this->render('admin/inventory/low_stock', [
            'page_title' => 'Low Stock Alerts',
            'products'   => $products,
        ]);
    }

    /**
     * Adjust stock for a single product.
     */
    public function adjust(int $id): void
    {
        $quantity = (int) $this->post('quantity', 0);
        $type = $this->post('type', 'add'); // add, subtract, set
        $notes = $this->post('notes', 'Manual adjustment');

        $product = $this->productModel->find($id);
        if (!$product) {
            $this->redirect('/admin/inventory', 'Product not found.', 'error');
        }

        if ($type === 'set') {
            $adjustment = $quantity - $product['stock'];
            $newStock = $quantity;
        } elseif ($type === 'subtract') {
            $adjustment = -$quantity;
            $newStock = max(0, $product['stock'] - $quantity);
        } else {
            $adjustment = $quantity;
            $newStock = $product['stock'] + $quantity;
        }

        // Update product table
        $this->productModel->update($id, ['stock' => $newStock]);

        // Log to inventory history
        $adminId = $_SESSION['user_id'];
        $this->inventoryModel->logChange($id, $adjustment, $type, $notes, $adminId);

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/inventory', "Stock updated successfully for {$product['name']}.", 'success');
    }
}
