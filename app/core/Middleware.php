<?php
/**
 * Middleware Class
 * ────────────────
 * Route middleware for authentication, authorization, and CSRF protection.
 */

class Middleware
{
    /**
     * Require authentication — redirect to login if not logged in.
     */
    public static function auth(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to continue.');
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            redirect_to(url('/login'));
        }
    }

    /**
     * Require admin role — redirect if not admin.
     */
    public static function admin(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please log in to access the admin panel.');
            redirect_to(url('/admin/login'));
        }

        if (!Auth::isAdmin()) {
            Session::flash('error', 'Access denied. Admin privileges required.');
            redirect_to(url('/'));
        }
    }

    /**
     * Guest only — redirect to home if already logged in.
     */
    public static function guest(): void
    {
        if (Auth::check()) {
            redirect_to(url('/'));
        }
    }

    /**
     * Generate a CSRF token and store in session.
     *
     * @return string The CSRF token
     */
    public static function csrf(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    /**
     * Verify the CSRF token from a POST request.
     * Aborts with 403 if invalid.
     */
    public static function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = Session::get('csrf_token', '');

        if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            die('Invalid CSRF token. Please refresh the page and try again.');
        }

        // Regenerate token after successful verification (single-use tokens)
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }

    /**
     * Rate limiting for login attempts.
     * Blocks if more than $maxAttempts in $windowMinutes.
     *
     * @param int $maxAttempts Maximum failed attempts allowed
     * @param int $windowMinutes Time window in minutes
     */
    public static function rateLimit(int $maxAttempts = 5, int $windowMinutes = 15): void
    {
        $key = 'login_attempts';
        $timeKey = 'login_attempt_time';

        $attempts = Session::get($key, 0);
        $firstAttemptTime = Session::get($timeKey, time());

        // Reset if window has passed
        if (time() - $firstAttemptTime > ($windowMinutes * 60)) {
            Session::set($key, 0);
            Session::set($timeKey, time());
            return;
        }

        if ($attempts >= $maxAttempts) {
            $remainingMinutes = ceil(($windowMinutes * 60 - (time() - $firstAttemptTime)) / 60);
            Session::flash('error', "Too many login attempts. Please try again in {$remainingMinutes} minute(s).");
            redirect_to(url('/login'));
        }
    }

    /**
     * Increment failed login attempt counter.
     */
    public static function incrementLoginAttempts(): void
    {
        $attempts = Session::get('login_attempts', 0);
        if ($attempts === 0) {
            Session::set('login_attempt_time', time());
        }
        Session::set('login_attempts', $attempts + 1);
    }

    /**
     * Reset login attempt counter (call on successful login).
     */
    public static function resetLoginAttempts(): void
    {
        Session::remove('login_attempts');
        Session::remove('login_attempt_time');
    }
}
