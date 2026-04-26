<?php
/**
 * Authentication Class
 * ────────────────────
 * Handles user login, logout, and session-based authentication.
 * Uses bcrypt for password hashing and PDO for database queries.
 */

class Auth
{
    /**
     * Attempt to log in a user.
     *
     * @param string $email
     * @param string $password
     * @return bool True on success, false on failure
     */
    public static function login(string $email, string $password): bool
    {
        $sql = "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // Regenerate session ID to prevent session fixation
        Session::regenerate(true);

        // Store user data in session
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('logged_in', true);

        return true;
    }

    /**
     * Log out the current user.
     */
    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * Check if a user is currently logged in.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return Session::get('logged_in', false) === true;
    }

    /**
     * Get the current authenticated user's data from the database.
     *
     * @return array|null
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $sql = "SELECT id, name, email, phone, role, status, created_at FROM users WHERE id = ? LIMIT 1";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([self::id()]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Check if the logged-in user is an admin.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::check() && Session::get('user_role') === 'admin';
    }

    /**
     * Get the logged-in user's ID.
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        return self::check() ? (int) Session::get('user_id') : null;
    }

    /**
     * Get the logged-in user's name.
     *
     * @return string|null
     */
    public static function name(): ?string
    {
        return self::check() ? Session::get('user_name') : null;
    }

    /**
     * Get the logged-in user's email.
     *
     * @return string|null
     */
    public static function email(): ?string
    {
        return self::check() ? Session::get('user_email') : null;
    }

    /**
     * Get the logged-in user's role.
     *
     * @return string|null
     */
    public static function role(): ?string
    {
        return self::check() ? Session::get('user_role') : null;
    }
}
