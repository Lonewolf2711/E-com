<?php
/**
 * Base Model
 * ──────────
 * Abstract base class for all database models.
 * Provides common CRUD operations using PDO prepared statements.
 */

class Model
{
    /**
     * @var string The database table name (set in child class)
     */
    protected string $table = '';

    /**
     * @var string The primary key column name
     */
    protected string $primaryKey = 'id';

    /**
     * @var PDO Database connection
     */
    protected PDO $db;

    /**
     * Constructor — get the PDO singleton.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key.
     *
     * @param int $id
     * @return array|false
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find all records, optionally ordered.
     *
     * @param string $orderBy Column to order by
     * @param string $direction ASC or DESC
     * @return array
     */
    public function findAll(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new record.
     *
     * @param array $data Associative array of column => value
     * @return int The new record's ID
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a record by primary key.
     *
     * @param int   $id
     * @param array $data Associative array of column => value
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setParts = [];
        $values = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Delete a record by primary key.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Execute a custom query with parameters.
     *
     * @param string $sql
     * @param array  $params
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Paginate results.
     *
     * @param int    $page    Current page (1-based)
     * @param int    $perPage Items per page
     * @param string $orderBy Column to order by
     * @param string $direction ASC or DESC
     * @param string $where   Optional WHERE clause (without 'WHERE' keyword)
     * @param array  $params  Bind parameters for WHERE clause
     * @return array ['data' => [], 'total' => int, 'pages' => int, 'current_page' => int, 'per_page' => int]
     */
    public function paginate(
        int $page = 1,
        int $perPage = 12,
        string $orderBy = 'id',
        string $direction = 'DESC',
        string $where = '',
        array $params = []
    ): array {
        $page = max(1, $page);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $whereClause = $where ? "WHERE {$where}" : '';

        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch page
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY {$orderBy} {$direction} LIMIT ? OFFSET ?";
        $fetchParams = array_merge($params, [$perPage, $offset]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($fetchParams);
        $data = $stmt->fetchAll();

        return [
            'data'         => $data,
            'total'        => $total,
            'pages'        => (int) ceil($total / $perPage),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Count all records, optionally with a WHERE clause.
     *
     * @param string $where  Optional WHERE clause
     * @param array  $params Bind parameters
     * @return int
     */
    public function count(string $where = '', array $params = []): int
    {
        $whereClause = $where ? "WHERE {$where}" : '';
        $sql = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Find records matching a WHERE clause.
     *
     * @param string $where
     * @param array  $params
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public function where(string $where, array $params = [], string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} {$direction}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Find a single record matching a WHERE clause.
     *
     * @param string $where
     * @param array  $params
     * @return array|false
     */
    public function findWhere(string $where, array $params = []): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$where} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
