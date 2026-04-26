<?php
/**
 * User Model
 * ──────────
 * Handles user CRUD, authentication queries, and role management.
 * Table: users
 */

class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): array|false
    {
        return $this->findWhere('email = ?', [$email]);
    }

    /**
     * Create a new user with hashed password.
     */
    public function register(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->create($data);
    }

    /**
     * Get all customers (role = customer).
     */
    public function getCustomers(int $page = 1, int $perPage = 15): array
    {
        return $this->paginate($page, $perPage, 'created_at', 'DESC', "role = 'customer'");
    }

    /**
     * Get all admins.
     */
    public function getAdmins(): array
    {
        return $this->where("role = 'admin'", [], 'name', 'ASC');
    }

    /**
     * Update user status (active/banned).
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->update($id, ['password' => $hashed]);
    }

    /**
     * Search customers by name or email.
     */
    public function searchCustomers(string $query): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE role = 'customer' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)
                ORDER BY name ASC";
        $like = "%{$query}%";
        return $this->query($sql, [$like, $like, $like])->fetchAll();
    }

    /**
     * Get customer stats for admin dashboard.
     */
    public function getCustomerStats(): array
    {
        $db = Database::getInstance();

        $total = $this->count("role = 'customer'");
        $active = $this->count("role = 'customer' AND status = 'active'");

        // New customers this month
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM users
             WHERE role = 'customer' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
        );
        $stmt->execute();
        $thisMonth = (int) $stmt->fetchColumn();

        return [
            'total'      => $total,
            'active'     => $active,
            'this_month' => $thisMonth,
        ];
    }

    /**
     * Get top customers by total spend.
     */
    public function getTopCustomers(int $limit = 10): array
    {
        $sql = "SELECT u.id, u.name, u.email, u.phone, u.created_at,
                       COUNT(o.id) as total_orders,
                       COALESCE(SUM(o.total_amount), 0) as total_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'paid'
                WHERE u.role = 'customer'
                GROUP BY u.id
                ORDER BY total_spent DESC
                LIMIT ?";
        return $this->query($sql, [$limit])->fetchAll();
    }
}
