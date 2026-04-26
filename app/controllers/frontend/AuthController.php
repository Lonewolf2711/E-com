<?php
/**
 * Frontend Auth Controller
 * ────────────────────────
 * Handles customer login, registration, logout, and profile.
 */

class FrontendAuthController extends Controller
{
    /**
     * Show login form.
     */
    public function loginForm(): void
    {
        $this->render('frontend/auth/login', [
            'page_title' => 'Login',
        ]);
    }

    /**
     * Process login.
     */
    public function login(): void
    {
        Middleware::verifyCsrf();
        Middleware::rateLimit(5, 15);

        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');

        // Validate
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please enter both email and password.');
            Session::flash('old_email', $email);
            $this->redirect(url('/login'));
            return;
        }

        // Attempt login
        if (Auth::login($email, $password)) {
            Middleware::resetLoginAttempts();

            // Merge guest cart
            $cart = new Cart();
            $cart->mergeGuestCart(Auth::id(), session_id());

            Session::flash('success', 'Welcome back, ' . Auth::name() . '!');

            // Redirect to intended URL or home
            $intended = Session::get('intended_url');
            Session::remove('intended_url');

            if (Auth::isAdmin()) {
                $this->redirect($intended ?: url('/admin'));
            } else {
                $this->redirect($intended ?: url('/'));
            }
        } else {
            Middleware::incrementLoginAttempts();
            Session::flash('error', 'Invalid email or password.');
            Session::flash('old_email', $email);
            $this->redirect(url('/login'));
        }
    }

    /**
     * Show registration form.
     */
    public function registerForm(): void
    {
        $this->render('frontend/auth/register', [
            'page_title' => 'Register',
        ]);
    }

    /**
     * Process registration.
     */
    public function register(): void
    {
        Middleware::verifyCsrf();

        $name = trim($this->post('name', ''));
        $email = trim($this->post('email', ''));
        $phone = trim($this->post('phone', ''));
        $password = $this->post('password', '');
        $confirmPassword = $this->post('confirm_password', '');

        // Store old input for re-population
        Session::flash('old_name', $name);
        Session::flash('old_email', $email);
        Session::flash('old_phone', $phone);

        // Validate
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required.';
        if (empty($email)) $errors[] = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

        // Check if email already exists
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect(url('/register'));
            return;
        }

        // Create user
        try {
            $userId = $userModel->register([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'password' => $password,
                'role'     => 'customer',
                'status'   => 'active',
            ]);

            // Auto login
            Auth::login($email, $password);

            // Merge guest cart
            $cart = new Cart();
            $cart->mergeGuestCart($userId, session_id());

            Session::flash('success', 'Account created successfully! Welcome, ' . $name . '!');
            $this->redirect(url('/'));
        } catch (Exception $e) {
            Session::flash('error', 'Registration failed. Please try again.');
            $this->redirect(url('/register'));
        }
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        Auth::logout();
        // Start a new session for flash message
        Session::start();
        Session::flash('success', 'You have been logged out.');
        $this->redirect(url('/login'));
    }

    /**
     * Show profile/account page.
     */
    public function profile(): void
    {
        $user = Auth::user();
        $orderModel = new Order();
        $recentOrders = $orderModel->getUserOrders(Auth::id(), 1, 5);
        $wishlistModel = new Wishlist();
        $wishlistCount = $wishlistModel->getUserCount(Auth::id());

        $this->render('frontend/auth/account', [
            'page_title'     => 'My Account',
            'user'           => $user,
            'recent_orders'  => $recentOrders,
            'wishlist_count' => $wishlistCount,
        ]);
    }

    /**
     * Update profile.
     */
    public function updateProfile(): void
    {
        $name = trim($this->post('name', ''));
        $phone = trim($this->post('phone', ''));
        $currentPassword = $this->post('current_password', '');
        $newPassword = $this->post('new_password', '');

        $userModel = new User();
        $updateData = ['name' => $name, 'phone' => $phone];

        // If changing password
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                Session::flash('error', 'Please enter your current password to set a new one.');
                $this->redirect(url('/account'));
                return;
            }

            // Verify current password
            $user = $userModel->find(Auth::id());
            if (!password_verify($currentPassword, $user['password'])) {
                Session::flash('error', 'Current password is incorrect.');
                $this->redirect(url('/account'));
                return;
            }

            if (strlen($newPassword) < 6) {
                Session::flash('error', 'New password must be at least 6 characters.');
                $this->redirect(url('/account'));
                return;
            }

            $userModel->updatePassword(Auth::id(), $newPassword);
        }

        $userModel->update(Auth::id(), $updateData);

        // Update session name
        Session::set('user_name', $name);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect(url('/account'));
    }
}
