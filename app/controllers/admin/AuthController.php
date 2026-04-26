<?php
/**
 * Admin Auth Controller
 * ─────────────────────
 * Handles admin login page (uses Mazer template auth layout).
 */

class AdminAuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function loginForm(): void
    {
        // If already logged in as admin, redirect to dashboard
        if (is_logged_in() && Auth::isAdmin()) {
            $this->redirect(url('/admin/dashboard'));
            return;
        }

        $this->render('admin/auth/login', [
            'page_title' => 'Admin Login',
        ]);
    }

    /**
     * Process admin login.
     */
    public function login(): void
    {
        Middleware::verifyCsrf();
        Middleware::rateLimit(5, 15);

        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please enter both email and password.');
            $this->redirect(url('/admin/login'));
            return;
        }

        if (Auth::login($email, $password)) {
            if (!Auth::isAdmin()) {
                Auth::logout();
                Session::start();
                Session::flash('error', 'You do not have admin privileges.');
                $this->redirect(url('/admin/login'));
                return;
            }

            Middleware::resetLoginAttempts();
            Session::flash('success', 'Welcome back, ' . Auth::name() . '!');
            $this->redirect(url('/admin/dashboard'));
        } else {
            Middleware::incrementLoginAttempts();
            Session::flash('error', 'Invalid email or password.');
            $this->redirect(url('/admin/login'));
        }
    }
}
