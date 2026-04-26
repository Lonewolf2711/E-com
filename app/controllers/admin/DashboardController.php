<?php
/**
 * Admin Dashboard Controller
 * ──────────────────────────
 * Main admin landing page with stats, charts, and recent activity.
 */

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        $orderModel = new Order();
        $userModel = new User();
        $productModel = new Product();
        $reviewModel = new Review();

        $orderStats = $orderModel->getStats();
        
        // Summary statistics
        $stats = [
            'total_orders'      => $orderStats['total'],
            'total_revenue'     => $orderStats['total_revenue'],
            'total_customers'   => $userModel->count("role = 'customer'"),
            'total_products'    => $productModel->count(),
            'pending_orders'    => $orderStats['pending'],
            'processing_orders' => $orderStats['processing'],
        ];

        // Recent orders (last 10)
        $recentOrders = $orderModel->getRecent(10);

        // Monthly sales data for chart (last 6 months)
        $salesChart = $orderModel->getMonthlySales(6);

        // Top selling products
        $topProducts = $productModel->getBestSellers(5);

        // Low stock alerts
        $lowStock = $productModel->getLowStock(1, 10)['data'] ?? [];

        // Pending reviews
        $pendingReviews = $reviewModel->count("status = 'pending'");

        $this->render('admin/dashboard', [
            'page_title'      => 'Dashboard',
            'stats'           => $stats,
            'recent_orders'   => $recentOrders,
            'sales_chart'     => $salesChart,
            'top_products'    => $topProducts,
            'low_stock'       => $lowStock,
            'pending_reviews' => $pendingReviews,
        ]);
    }
}
