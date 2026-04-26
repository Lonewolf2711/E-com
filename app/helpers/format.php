<?php
/**
 * Formatting Helper Functions
 * ───────────────────────────
 * Number, date, string formatting utilities.
 */

/**
 * Format a price with currency symbol.
 *
 * @param float  $amount   The price amount
 * @param string $currency Currency code (optional, falls back to setting)
 * @return string Formatted price string
 */
function formatPrice(float $amount, string $currency = ''): string
{
    $symbol = $currency ?: (defined('DEFAULT_CURRENCY_SYMBOL') ? DEFAULT_CURRENCY_SYMBOL : '₹');
    return $symbol . number_format($amount, 2);
}

/**
 * Format a date string.
 *
 * @param string $date   Date string (from database)
 * @param string $format PHP date format
 * @return string Formatted date
 */
function formatDate(string $date, string $format = 'd M Y'): string
{
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format a date with time.
 *
 * @param string $date Date string
 * @return string Formatted date and time
 */
function formatDateTime(string $date): string
{
    return formatDate($date, 'd M Y, h:i A');
}

/**
 * Get relative time string (e.g., "2 hours ago").
 *
 * @param string $date Date string
 * @return string Relative time
 */
function timeAgo(string $date): string
{
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';

    return formatDate($date);
}

/**
 * Truncate a string to a maximum length.
 *
 * @param string $str String to truncate
 * @param int    $len Maximum length
 * @param string $suffix Suffix to append if truncated
 * @return string Truncated string
 */
function truncate(string $str, int $len = 100, string $suffix = '...'): string
{
    if (mb_strlen($str) <= $len) {
        return $str;
    }
    return mb_substr($str, 0, $len) . $suffix;
}

/**
 * Generate a URL-safe slug from a string.
 *
 * @param string $str String to slugify
 * @return string URL slug
 */
function slugify(string $str): string
{
    // Convert to lowercase
    $slug = mb_strtolower($str);

    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);

    // Replace multiple consecutive hyphens with a single one
    $slug = preg_replace('/-+/', '-', $slug);

    // Trim hyphens from start and end
    return trim($slug, '-');
}

/**
 * Format file size (bytes to human readable).
 *
 * @param int $bytes File size in bytes
 * @return string Formatted size
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Format order status with Bootstrap badge class.
 *
 * @param string $status Order status
 * @return array ['class' => string, 'label' => string]
 */
function orderStatusBadge(string $status): array
{
    $badges = [
        'pending'    => ['class' => 'bg-warning',   'label' => 'Pending'],
        'confirmed'  => ['class' => 'bg-info',      'label' => 'Confirmed'],
        'packed'     => ['class' => 'bg-primary',   'label' => 'Packed'],
        'shipped'    => ['class' => 'bg-secondary',  'label' => 'Shipped'],
        'delivered'  => ['class' => 'bg-success',   'label' => 'Delivered'],
        'cancelled'  => ['class' => 'bg-danger',    'label' => 'Cancelled'],
    ];

    return $badges[$status] ?? ['class' => 'bg-secondary', 'label' => ucfirst($status)];
}

/**
 * Format payment status with Bootstrap badge class.
 *
 * @param string $status Payment status
 * @return array ['class' => string, 'label' => string]
 */
function paymentStatusBadge(string $status): array
{
    $badges = [
        'pending'  => ['class' => 'bg-warning',  'label' => 'Pending'],
        'paid'     => ['class' => 'bg-success',  'label' => 'Paid'],
        'success'  => ['class' => 'bg-success',  'label' => 'Success'],
        'failed'   => ['class' => 'bg-danger',   'label' => 'Failed'],
        'refunded' => ['class' => 'bg-secondary', 'label' => 'Refunded'],
    ];

    return $badges[$status] ?? ['class' => 'bg-secondary', 'label' => ucfirst($status)];
}

/**
 * Generate star rating HTML.
 *
 * @param float $rating Rating value (0-5)
 * @return string HTML stars
 */
function starRating(float $rating): string
{
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fas fa-star text-primary"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<i class="fas fa-star-half-alt text-primary"></i>';
        } else {
            $html .= '<i class="fas fa-star text-muted"></i>';
        }
    }
    return $html;
}
