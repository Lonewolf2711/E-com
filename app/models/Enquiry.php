<?php
/**
 * Enquiry Model
 * ─────────────
 * Represents an Enquiry submission containing contact details
 * and a JSON snapshot of the cart at the moment of submission.
 */

class Enquiry extends Model
{
    protected string $table = 'enquiries';

    /**
     * Get all enquiries, supporting search filter.
     */
    public function getAll(string $search = '', int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $whereFilters = [];
        $params = [];

        if (!empty($search)) {
            $whereFilters[] = "(customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ? OR enquiry_number LIKE ?)";
            $wildcard = "%{$search}%";
            array_push($params, $wildcard, $wildcard, $wildcard, $wildcard);
        }

        if (!empty($status)) {
            $whereFilters[] = "status = ?";
            $params[] = $status;
        }

        $whereClause = implode(" AND ", $whereFilters);
        
        return $this->paginate($page, $perPage, 'created_at', 'DESC', $whereClause, $params);
    }

    /**
     * Get single enquiry by ID.
     */
    public function getById(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Update the status of an enquiry.
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get summary statistics for the dashboard.
     */
    public function getStats(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN `status` = 'new' THEN 1 ELSE 0 END) as new_enquiries,
                SUM(CASE WHEN `status` = 'acknowledged' THEN 1 ELSE 0 END) as acknowledged_enquiries,
                SUM(CASE WHEN `status` = 'quoted' THEN 1 ELSE 0 END) as quoted_enquiries,
                SUM(CASE WHEN `status` = 'closed' THEN 1 ELSE 0 END) as closed_enquiries
            FROM enquiries
        ");
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0,
            'new_enquiries' => 0,
            'acknowledged_enquiries' => 0,
            'quoted_enquiries' => 0,
            'closed_enquiries' => 0
        ];
    }
}
