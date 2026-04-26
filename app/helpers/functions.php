<?php
/**
 * Global Helper Functions
 * ───────────────────────
 * Utility functions available throughout the application.
 */

/**
 * Generate a full URL from a relative path.
 *
 * @param string $path Relative path (e.g., '/shop')
 * @return string Full URL
 */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/' . $path;
}

/**
 * Generate a full URL for a static asset.
 *
 * @param string $path Asset path relative to public/assets/ (e.g., 'frontend/css/style.css')
 * @return string Full asset URL
 */
function asset(string $path = ''): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/public/assets/' . $path;
}

/**
 * Generate URL for an uploaded file.
 *
 * @param string $path Upload path relative to public/uploads/
 * @return string Full upload URL
 */
function upload_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . '/public/uploads/' . $path;
}

/**
 * Dump and die — debug helper.
 *
 * @param mixed $var Variable to inspect
 */
function dd(mixed $var): void
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Escape output for HTML (XSS prevention).
 *
 * @param string|null $str String to escape
 * @return string Escaped string
 */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Get old form input (from flash session data).
 *
 * @param string $key     Form field name
 * @param string $default Default value if not found
 * @return string
 */
function old(string $key, string $default = ''): string
{
    return Session::getFlash('old_' . $key, $default);
}

/**
 * Redirect to a URL and exit.
 *
 * @param string $url Full URL to redirect to
 */
function redirect_to(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Check if user is logged in.
 *
 * @return bool
 */
function is_logged_in(): bool
{
    return Auth::check();
}

/**
 * Get the current authenticated user data.
 *
 * @return array|null
 */
function auth_user(): ?array
{
    return Auth::user();
}

/**
 * Generate a CSRF hidden input field.
 *
 * @return string HTML hidden input
 */
function csrf_field(): string
{
    $token = Middleware::csrf();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token(): string
{
    return Middleware::csrf();
}

/**
 * Check if current URL matches the given path (for active nav highlighting).
 *
 * @param string $path URL path to check against
 * @return bool
 */
function is_active(string $path): bool
{
    $currentUri = trim($_GET['url'] ?? '', '/');
    $path = trim($path, '/');
    return $currentUri === $path;
}

/**
 * Check if current URL starts with the given path.
 *
 * @param string $path URL path prefix
 * @return bool
 */
function starts_with(string $path): bool
{
    $currentUri = trim($_GET['url'] ?? '', '/');
    $path = trim($path, '/');
    return str_starts_with($currentUri, $path);
}

/**
 * Get site setting value.
 *
 * @param string $key     Setting key
 * @param string $default Default value
 * @return string
 */
function get_setting(string $key, string $default = ''): string
{
    static $cache = [];
    if (!isset($cache[$key])) {
        try {
            $stmt = Database::getInstance()->prepare(
                "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1"
            );
            $stmt->execute([$key]);
            $cache[$key] = $stmt->fetchColumn() ?: $default;
        } catch (Exception $e) {
            $cache[$key] = $default;
        }
    }
    return $cache[$key];
}

/**
 * Generate pagination HTML (Bootstrap 5 compatible).
 *
 * @param int    $currentPage Current page number
 * @param int    $totalPages  Total number of pages
 * @param string $baseUrl     Base URL for pagination links
 * @param array  $queryParams Additional query parameters to preserve
 * @return string HTML pagination
 */
function pagination_html(int $currentPage, int $totalPages, string $baseUrl, array $queryParams = []): string
{
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $prevPage = max(1, $currentPage - 1);
    $queryParams['page'] = $prevPage;
    $prevUrl = $baseUrl . '?' . http_build_query($queryParams);
    $html .= "<li class=\"page-item {$prevDisabled}\"><a class=\"page-link\" href=\"{$prevUrl}\">&laquo;</a></li>";

    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $queryParams['page'] = 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? 'active' : '';
        $queryParams['page'] = $i;
        $pageUrl = $baseUrl . '?' . http_build_query($queryParams);
        $html .= "<li class=\"page-item {$active}\"><a class=\"page-link\" href=\"{$pageUrl}\">{$i}</a></li>";
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $queryParams['page'] = $totalPages;
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . http_build_query($queryParams) . '">' . $totalPages . '</a></li>';
    }

    // Next
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $nextPage = min($totalPages, $currentPage + 1);
    $queryParams['page'] = $nextPage;
    $nextUrl = $baseUrl . '?' . http_build_query($queryParams);
    $html .= "<li class=\"page-item {$nextDisabled}\"><a class=\"page-link\" href=\"{$nextUrl}\">&raquo;</a></li>";

    $html .= '</ul></nav>';

    return $html;
}

/**
 * Get current full canonical URL.
 *
 * @return string Full URL of the current request
 */
function current_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    return $protocol . $host . $requestUri;
}



/**
 * Convert any YouTube URL to an embed URL.
 *
 * Supports:
 *   https://youtu.be/VIDEOID
 *   https://www.youtube.com/watch?v=VIDEOID
 *   https://www.youtube.com/embed/VIDEOID (passthrough)
 *
 * @param string $url YouTube URL
 * @return string     YouTube embed URL or original URL if not YouTube
 */
function youtube_embed_url(string $url): string
{
    $videoId = null;

    // youtu.be short links
    if (preg_match('~youtu\.be/([a-zA-Z0-9_\-]{11})~', $url, $m)) {
        $videoId = $m[1];
    }
    // Standard watch URL
    elseif (preg_match('~[?&]v=([a-zA-Z0-9_\-]{11})~', $url, $m)) {
        $videoId = $m[1];
    }
    // Already an embed URL — extract ID
    elseif (preg_match('~youtube\.com/embed/([a-zA-Z0-9_\-]{11})~', $url, $m)) {
        $videoId = $m[1];
    }

    if ($videoId) {
        return 'https://www.youtube.com/embed/' . $videoId;
    }

    // Not a recognisable YouTube URL — return as-is
    return $url;
}
