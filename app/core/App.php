<?php
/**
 * App Class
 * ─────────
 * Application bootstrap: registers all routes and dispatches the router.
 */

class App
{
    private static Router $router;

    /**
     * Run the application.
     */
    public static function run(): void
    {
        self::$router = new Router();
        self::registerRoutes();
        self::$router->dispatch();
    }

    /**
     * Register all application routes.
     */
    private static function registerRoutes(): void
    {
        $r = self::$router;

        // ═══════════════════════════════════════════
        // FRONTEND ROUTES
        // ═══════════════════════════════════════════

        // ─── Home ───
        $r->get('', 'FrontendHomeController@index');
        $r->get('/', 'FrontendHomeController@index');

        // ─── Shop & Products ───
        $r->get('shop', 'FrontendShopController@index');
        $r->get('search', 'FrontendShopController@search');
        $r->get('product/{slug}', 'FrontendProductController@show');
        $r->post('product/{slug}/review', 'FrontendProductController@review', ['Middleware::auth', 'Middleware::verifyCsrf']);

        // ─── Categories ───
        $r->get('category/{slug}', 'FrontendCategoryController@show');

        // ─── Enquiry ───
        $r->post('enquiry/submit', 'FrontendEnquiryController@submit');

        // ─── Cart ───
        $r->get('cart', 'FrontendCartController@index');
        $r->post('cart/add', 'FrontendCartController@add');
        $r->post('cart/update', 'FrontendCartController@update');
        $r->post('cart/remove', 'FrontendCartController@remove');

        // ─── Account & Auth Redirects ───
        $redirectEnquiry = function() {
            Session::flash('error', 'Enquiry-based service — no account needed.');
            redirect_to(url('/shop'));
        };

        $r->get('login', $redirectEnquiry);
        $r->post('login', $redirectEnquiry);
        $r->get('register', $redirectEnquiry);
        $r->post('register', $redirectEnquiry);
        $r->get('account', $redirectEnquiry);
        $r->post('account/update', $redirectEnquiry);
        $r->get('wishlist', $redirectEnquiry);
        $r->post('wishlist/toggle', $redirectEnquiry);
        $r->get('logout', $redirectEnquiry);
        $r->get('my-orders', $redirectEnquiry);
        $r->get('my-orders/{id}', $redirectEnquiry);
        $r->get('order-detail', $redirectEnquiry);

        // ─── Enquiry Flow Redirects (Phase 2) ───
        $r->get('checkout', $redirectEnquiry);
        $r->post('checkout/apply-coupon', $redirectEnquiry);
        $r->post('checkout/process', $redirectEnquiry);
        $r->get('order/success/{id}', $redirectEnquiry);
        $r->get('track-order', $redirectEnquiry);
        $r->post('payment/razorpay/create', $redirectEnquiry);
        $r->post('payment/razorpay/verify', $redirectEnquiry);
        $r->post('payment/razorpay/webhook', $redirectEnquiry);
        $r->post('payment/stripe/create', $redirectEnquiry);
        $r->get('payment/stripe/success', $redirectEnquiry);
        $r->get('payment/stripe/cancel', $redirectEnquiry);
        $r->post('payment/stripe/webhook', $redirectEnquiry);
        $r->post('payment/paypal/create', $redirectEnquiry);
        $r->get('payment/paypal/success', $redirectEnquiry);
        $r->get('payment/paypal/cancel', $redirectEnquiry);

        // ─── Contact ───
        $r->get('contact', 'FrontendContactController@index');
        $r->post('contact', 'FrontendContactController@submit');

        // ─── Sitemap ───
        $r->get('sitemap.xml', 'FrontendSitemapController@index');

        // ═══════════════════════════════════════════
        // ADMIN ROUTES
        // ═══════════════════════════════════════════

        // ─── Admin Auth ───
        $r->get('admin/login', 'AdminAuthController@loginForm');
        $r->post('admin/login', 'AdminAuthController@login');

        // ─── Dashboard ───
        $r->get('admin', 'AdminDashboardController@index', ['Middleware::admin']);
        $r->get('admin/dashboard', 'AdminDashboardController@index', ['Middleware::admin']);

        // ─── Products ───
        $r->get('admin/products', 'AdminProductController@index', ['Middleware::admin']);
        $r->get('admin/products/add', 'AdminProductController@addForm', ['Middleware::admin']);
        $r->post('admin/products/store', 'AdminProductController@store', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->get('admin/products/edit/{id}', 'AdminProductController@editForm', ['Middleware::admin']);
        $r->post('admin/products/update/{id}', 'AdminProductController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/products/delete/{id}', 'AdminProductController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Categories ───
        $r->get('admin/categories', 'AdminCategoryController@index', ['Middleware::admin']);
        $r->post('admin/categories/store', 'AdminCategoryController@store', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/categories/update/{id}', 'AdminCategoryController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/categories/delete/{id}', 'AdminCategoryController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Enquiries ───
        $r->get('admin/enquiries', 'AdminEnquiryController@index', ['Middleware::admin']);
        $r->get('admin/enquiries/{id}', 'AdminEnquiryController@detail', ['Middleware::admin']);
        $r->post('admin/enquiries/status/{id}', 'AdminEnquiryController@updateStatus', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/enquiries/delete/{id}', 'AdminEnquiryController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/enquiries/send-email/{id}', 'AdminEnquiryController@sendEmail', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── AI Code ───
        $r->post('admin/ai/generate-quote', 'AdminAiController@generateQuoteEmail', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Contacts ───
        $r->get('admin/contacts', 'AdminContactController@index', ['Middleware::admin']);
        $r->get('admin/contacts/{id}', 'AdminContactController@detail', ['Middleware::admin']);
        $r->post('admin/contacts/toggle-read/{id}', 'AdminContactController@toggleRead', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/contacts/delete/{id}', 'AdminContactController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Email Templates ───
        $r->get('admin/email-templates', 'AdminEmailTemplateController@index', ['Middleware::admin']);
        $r->get('admin/email-templates/create', 'AdminEmailTemplateController@createForm', ['Middleware::admin']);
        $r->post('admin/email-templates/store', 'AdminEmailTemplateController@store', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->get('admin/email-templates/edit/{id}', 'AdminEmailTemplateController@editForm', ['Middleware::admin']);
        $r->post('admin/email-templates/update/{id}', 'AdminEmailTemplateController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/email-templates/delete/{id}', 'AdminEmailTemplateController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->get('admin/email-templates/preview/{id}', 'AdminEmailTemplateController@preview', ['Middleware::admin']);

        // ─── Orders (Disabled for phase 3) ───
        // $r->get('admin/orders', 'AdminOrderController@index', ['Middleware::admin']);
        // $r->get('admin/orders/{id}', 'AdminOrderController@detail', ['Middleware::admin']);
        // $r->post('admin/orders/{id}/status', 'AdminOrderController@updateStatus', ['Middleware::admin', 'Middleware::verifyCsrf']);
        // $r->post('admin/orders/{id}/tracking', 'AdminOrderController@addTracking', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Customers ───
        $r->get('admin/customers', 'AdminCustomerController@index', ['Middleware::admin']);
        $r->get('admin/customers/{id}', 'AdminCustomerController@detail', ['Middleware::admin']);
        $r->post('admin/customers/{id}/status', 'AdminCustomerController@updateStatus', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Inventory ───
        $r->get('admin/inventory', 'AdminInventoryController@index', ['Middleware::admin']);
        $r->get('admin/inventory/low-stock', 'AdminInventoryController@lowStock', ['Middleware::admin']);
        $r->post('admin/inventory/adjust/{id}', 'AdminInventoryController@adjust', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Coupons ───
        $r->get('admin/coupons', 'AdminCouponController@index', ['Middleware::admin']);
        $r->post('admin/coupons/store', 'AdminCouponController@store', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/coupons/delete/{id}', 'AdminCouponController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->get('admin/coupons/campaigns', 'AdminCouponController@campaigns', ['Middleware::admin']);

        // ─── Payments ───
        $r->get('admin/payments', 'AdminPaymentController@index', ['Middleware::admin']);
        $r->get('admin/payments/transactions', 'AdminPaymentController@transactions', ['Middleware::admin']);

        // ─── Reports ───
        $r->get('admin/reports/sales', 'AdminReportController@sales', ['Middleware::admin']);
        $r->get('admin/reports/products', 'AdminReportController@products', ['Middleware::admin']);
        $r->get('admin/reports/customers', 'AdminReportController@customers', ['Middleware::admin']);

        // ─── SEO ───
        $r->get('admin/seo', 'AdminSeoController@index', ['Middleware::admin']);
        $r->post('admin/seo/update/{type}/{id}', 'AdminSeoController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Banners ───
        $r->get('admin/banners', 'AdminBannerController@index', ['Middleware::admin']);
        $r->get('admin/banners/add', 'AdminBannerController@addForm', ['Middleware::admin']);
        $r->post('admin/banners/store', 'AdminBannerController@store', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->get('admin/banners/edit/{id}', 'AdminBannerController@editForm', ['Middleware::admin']);
        $r->post('admin/banners/update/{id}', 'AdminBannerController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);
        $r->post('admin/banners/delete/{id}', 'AdminBannerController@delete', ['Middleware::admin', 'Middleware::verifyCsrf']);

        // ─── Settings ───
        $r->get('admin/settings', 'AdminSettingController@index', ['Middleware::admin']);
        $r->post('admin/settings/update', 'AdminSettingController@update', ['Middleware::admin', 'Middleware::verifyCsrf']);
    }
}
