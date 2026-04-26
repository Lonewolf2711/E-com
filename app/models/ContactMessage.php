<?php
/**
 * ContactMessage Model
 * ─────────────────────
 * Represents a contact form submission from the website.
 */

class ContactMessage extends Model
{
    protected string $table = 'contact_messages';

    /**
     * Get all messages with optional search and read-filter.
     */
    public function getAll(string $search = '', int $page = 1, int $perPage = 20, string $filter = ''): array
    {
        $whereFilters = [];
        $params = [];

        if (!empty($search)) {
            $whereFilters[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
            $wildcard = "%{$search}%";
            array_push($params, $wildcard, $wildcard, $wildcard);
        }

        if ($filter === 'unread') {
            $whereFilters[] = "is_read = 0";
        } elseif ($filter === 'read') {
            $whereFilters[] = "is_read = 1";
        }

        $whereClause = implode(" AND ", $whereFilters);
        return $this->paginate($page, $perPage, 'created_at', 'DESC', $whereClause, $params);
    }

    /**
     * Get a single message by ID.
     */
    public function getById(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Mark a message as read.
     */
    public function markRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    /**
     * Mark a message as unread.
     */
    public function markUnread(int $id): bool
    {
        return $this->update($id, ['is_read' => 0]);
    }

    /**
     * Count of unread messages.
     */
    public function countUnread(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Save a new contact message.
     */
    public function saveMessage(string $name, string $email, string $subject, string $message): int|false
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        return (int) $db->lastInsertId();
    }
}
