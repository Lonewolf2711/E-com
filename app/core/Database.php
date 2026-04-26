<?php
/**
 * Database Class
 * ──────────────
 * PDO singleton for MySQL connections.
 * Ensures a single database connection throughout the application lifecycle.
 */

class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Get the singleton PDO instance.
     *
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                defined('DB_PORT') ? DB_PORT : '3306',
                DB_NAME,
                DB_CHARSET
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
                ]);
            } catch (PDOException $e) {
                if (ENVIRONMENT === 'development') {
                    die('Database connection failed: ' . $e->getMessage());
                } else {
                    error_log('Database connection failed: ' . $e->getMessage());
                    die('A database error occurred. Please try again later.');
                }
            }
        }

        return self::$instance;
    }

    /**
     * Convenience method: prepare and execute a query.
     *
     * @param string $sql    SQL query with placeholders
     * @param array  $params Bind parameters
     * @return PDOStatement
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get the last inserted ID.
     *
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Begin a transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Roll back a transaction.
     */
    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }
}
