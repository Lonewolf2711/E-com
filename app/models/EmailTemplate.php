<?php
/**
 * EmailTemplate Model
 * ─────────────────────
 * Reusable email templates for enquiry responses and notifications.
 * Supports {{placeholder}} tokens replaced at send time.
 */

class EmailTemplate extends Model
{
    protected string $table = 'email_templates';

    /**
     * Get all templates, optionally filtered by category.
     */
    public function getAll(string $category = '', int $page = 1, int $perPage = 50): array
    {
        $where  = '';
        $params = [];

        if (!empty($category)) {
            $where  = 'category = ?';
            $params = [$category];
        }

        return $this->paginate($page, $perPage, 'name', 'ASC', $where, $params);
    }

    /**
     * Get a single template by ID.
     */
    public function getById(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Get all templates as a flat list (for dropdown selectors).
     */
    public function getList(string $category = ''): array
    {
        $db  = Database::getInstance();
        $sql = "SELECT id, name, subject, category FROM email_templates";
        if (!empty($category)) {
            $sql .= " WHERE category = ?";
            $stmt = $db->prepare($sql . " ORDER BY name ASC");
            $stmt->execute([$category]);
        } else {
            $stmt = $db->prepare($sql . " ORDER BY category ASC, name ASC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resolve {{token}} placeholders in subject and body.
     */
    public function resolve(array $template, array $tokens): array
    {
        $search  = array_map(fn($k) => "{{$k}}", array_keys($tokens));
        $replace = array_values($tokens);

        return [
            'subject' => str_replace($search, $replace, $template['subject']),
            'body'    => str_replace($search, $replace, $template['body']),
        ];
    }

    /**
     * Save a new template.
     */
    public function saveTemplate(array $data): int
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO email_templates (name, subject, body, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['subject'], $data['body'], $data['category'] ?? 'general']);
        return (int) $db->lastInsertId();
    }

    /**
     * Update an existing template.
     */
    public function updateTemplate(int $id, array $data): bool
    {
        return $this->update($id, [
            'name'     => $data['name'],
            'subject'  => $data['subject'],
            'body'     => $data['body'],
            'category' => $data['category'] ?? 'general',
        ]);
    }
}
