<?php
/**
 * Admin Coupon Controller
 * ───────────────────────
 * Manages discount coupons, creation, deletion, and campaign tracking.
 */

class AdminCouponController extends Controller
{
    private Coupon $couponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
    }

    /**
     * List all coupons.
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $coupons = $this->couponModel->getAdminList($page, 15);

        $this->render('admin/coupons/index', [
            'page_title' => 'Coupons',
            'coupons'    => $coupons
        ]);
    }

    /**
     * Store new coupon.
     */
    public function store(): void
    {
        $code = strtoupper(trim($this->post('code')));
        if (empty($code)) {
            $this->redirect('/admin/coupons', 'Coupon code is required.', 'error');
        }

        if ($this->couponModel->findByCode($code)) {
            $this->redirect('/admin/coupons', 'Coupon code already exists.', 'error');
        }

        $data = [
            'code'             => $code,
            'type'             => $this->post('type', 'percent'),
            'discount_value'   => (float) $this->post('discount_value', 0),
            'min_order_amount' => (float) $this->post('min_order_amount', 0),
            'max_discount'     => (float) $this->post('max_discount', 0),
            'max_uses'         => (int) $this->post('max_uses', 0),
            'expiry_date'      => $this->post('expiry_date'),
            'status'           => $this->post('status', 'active'),
        ];

        // Validation for percent
        if ($data['type'] === 'percent' && $data['discount_value'] > 100) {
            $data['discount_value'] = 100;
        }

        if (empty($data['expiry_date'])) {
            $data['expiry_date'] = date('Y-m-d H:i:s', strtotime('+1 year')); // default 1 year
        }

        $this->couponModel->create($data);
        $this->redirect('/admin/coupons', 'Coupon created successfully.', 'success');
    }

    /**
     * Delete coupon.
     */
    public function delete(int $id): void
    {
        // Check if coupon has been used
        $coupon = $this->couponModel->find($id);
        if ($coupon && $coupon['used_count'] > 0) {
            // Soft deactivate instead of hard delete if used
            $this->couponModel->update($id, ['status' => 'inactive']);
            $this->redirect('/admin/coupons', 'Coupon marked as inactive because it has already been used.', 'warning');
        } else {
            $this->couponModel->delete($id);
            $this->redirect('/admin/coupons', 'Coupon deleted successfully.', 'success');
        }
    }

    /**
     * View campaign tracking/stats.
     */
    public function campaigns(): void
    {
        $page = (int) $this->get('page', 1);
        $coupons = $this->couponModel->getAdminList($page, 20);

        // Fetch revenue generated per coupon
        $db = Database::getInstance();
        $stats = [];
        foreach ($coupons['data'] as $c) {
            $stmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE coupon_id = ? AND payment_status = 'paid'");
            $stmt->execute([$c['id']]);
            $stats[$c['id']] = $stmt->fetchColumn();
        }

        $this->render('admin/coupons/campaigns', [
            'page_title' => 'Coupon Campaigns',
            'coupons'    => $coupons,
            'stats'      => $stats
        ]);
    }
}
