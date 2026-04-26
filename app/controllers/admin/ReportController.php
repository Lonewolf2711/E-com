<?php
/**
 * Admin Report Controller
 * ───────────────────────
 * Sales, product performance, and customer analytics reports.
 */

class AdminReportController extends Controller
{
    /**
     * Sales report — revenue by day/month, order counts, payment breakdown.
     */
    public function sales(): void
    {
        $db = Database::getInstance();
        $period = $this->get('period', '30'); // days

        // Daily revenue for the period
        $stmt = $db->prepare(
            "SELECT DATE(created_at) as date,
                    COUNT(*) as orders,
                    COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at)
             ORDER BY date ASC"
        );
        $stmt->execute([(int) $period]);
        $dailySales = $stmt->fetchAll();

        // Summary stats
        $stmt = $db->prepare(
            "SELECT COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_revenue,
                    COALESCE(AVG(total_amount), 0) as avg_order_value
             FROM orders
             WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        $stmt->execute([(int) $period]);
        $summary = $stmt->fetch();

        // Payment method breakdown
        $stmt = $db->prepare(
            "SELECT p.method, COUNT(*) as count, COALESCE(SUM(p.amount), 0) as total
             FROM payments p
             WHERE p.payment_status IN ('paid','success') AND p.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY p.method"
        );
        $stmt->execute([(int) $period]);
        $methodBreakdown = $stmt->fetchAll();

        $this->render('admin/reports/sales', [
            'page_title'      => 'Sales Report',
            'dailySales'      => $dailySales,
            'summary'         => $summary,
            'methodBreakdown' => $methodBreakdown,
            'period'          => $period,
        ]);
    }

    /**
     * Product report — best sellers, worst performers, category performance.
     */
    public function products(): void
    {
        $db = Database::getInstance();

        // Best selling products
        $stmt = $db->prepare(
            "SELECT p.id, p.name, p.sku, p.price, p.stock, c.name as category_name,
                    COALESCE(SUM(oi.quantity), 0) as units_sold,
                    COALESCE(SUM(oi.price * oi.quantity), 0) as revenue
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
             WHERE p.status = 'active'
             GROUP BY p.id
             ORDER BY units_sold DESC
             LIMIT 20"
        );
        $stmt->execute();
        $bestSellers = $stmt->fetchAll();

        // Category performance
        $stmt = $db->prepare(
            "SELECT c.name, COUNT(DISTINCT p.id) as product_count,
                    COALESCE(SUM(oi.quantity), 0) as units_sold,
                    COALESCE(SUM(oi.price * oi.quantity), 0) as revenue
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
             WHERE c.status = 'active'
             GROUP BY c.id
             ORDER BY revenue DESC"
        );
        $stmt->execute();
        $categoryPerf = $stmt->fetchAll();

        // Review stats
        $stmt = $db->prepare(
            "SELECT COUNT(*) as total, COALESCE(AVG(rating), 0) as avg_rating,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
             FROM reviews"
        );
        $stmt->execute();
        $reviewStats = $stmt->fetch();

        $this->render('admin/reports/products', [
            'page_title'   => 'Product Report',
            'bestSellers'  => $bestSellers,
            'categoryPerf' => $categoryPerf,
            'reviewStats'  => $reviewStats,
        ]);
    }

    /**
     * Customer report — registrations, top spenders, retention.
     */
    public function customers(): void
    {
        $db = Database::getInstance();

        // Monthly registrations (last 12 months)
        $stmt = $db->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
             FROM users WHERE role = 'customer'
             AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month ASC"
        );
        $stmt->execute();
        $registrations = $stmt->fetchAll();

        // Top spenders
        $userModel = new User();
        $topSpenders = $userModel->getTopCustomers(15);

        // Customer stats
        $stats = $userModel->getCustomerStats();

        $this->render('admin/reports/customers', [
            'page_title'    => 'Customer Report',
            'registrations' => $registrations,
            'topSpenders'   => $topSpenders,
            'stats'         => $stats,
        ]);
    }
}
