<?php
/**
 * Coupon Model
 * ────────────
 * Manages discount coupons with validation.
 * Table: coupons
 */

class Coupon extends Model
{
    protected string $table = 'coupons';

    /**
     * Find coupon by code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->findWhere('code = ?', [strtoupper($code)]);
    }

    /**
     * Validate and apply a coupon code.
     *
     * @return array ['valid' => bool, 'coupon' => array|null, 'discount' => float, 'error' => string]
     */
    public function validateCoupon(string $code, float $cartTotal): array
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'error' => 'Invalid coupon code.'];
        }

        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'error' => 'This coupon is no longer active.'];
        }

        if (strtotime($coupon['expiry_date']) < time()) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'error' => 'This coupon has expired.'];
        }

        if ($coupon['max_uses'] > 0 && $coupon['used_count'] >= $coupon['max_uses']) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'error' => 'This coupon has reached its usage limit.'];
        }

        if ($cartTotal < $coupon['min_order_amount']) {
            return [
                'valid' => false,
                'coupon' => null,
                'discount' => 0,
                'error' => 'Minimum order amount of ' . formatPrice($coupon['min_order_amount']) . ' is required.',
            ];
        }

        // Calculate discount
        $discount = 0;
        if ($coupon['type'] === 'percent') {
            $discount = ($cartTotal * $coupon['discount_value']) / 100;
            // Apply max discount cap if set
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = min($coupon['discount_value'], $cartTotal);
        }

        return [
            'valid'    => true,
            'coupon'   => $coupon,
            'discount' => round($discount, 2),
            'error'    => '',
        ];
    }

    /**
     * Increment used_count after order placement.
     */
    public function incrementUsage(int $couponId): bool
    {
        $sql = "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?";
        return $this->query($sql, [$couponId])->rowCount() > 0;
    }

    /**
     * Get active coupons.
     */
    public function getActive(): array
    {
        return $this->where("status = 'active' AND expiry_date >= CURDATE()", [], 'expiry_date', 'ASC');
    }

    /**
     * Get all coupons (admin, paginated).
     */
    public function getAdminList(int $page = 1, int $perPage = 15): array
    {
        return $this->paginate($page, $perPage, 'created_at', 'DESC');
    }
}
