<?php
/**
 * Frontend Sitemap Controller
 * ───────────────────────────
 * Generates dynamic XML sitemaps for SEO indexation.
 */

class FrontendSitemapController extends Controller
{
    public function index(): void
    {
        // Set proper Content-Type for XML
        header('Content-Type: application/xml; charset=utf-8');

        // Start XML declaration
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $baseUrl = rtrim(BASE_URL, '/');

        // Add static routes
        $staticRoutes = [
            ['loc' => '/', 'priority' => '1.0'],
            ['loc' => '/shop', 'priority' => '0.9'],
            ['loc' => '/contact', 'priority' => '0.7'],
        ];

        foreach ($staticRoutes as $route) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($baseUrl . $route['loc']) . "</loc>\n";
            $xml .= "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "        <priority>{$route['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }

        // Add products dynamically
        $productModel = new Product();
        $products = $productModel->where('status = ?', ['active'], 'updated_at', 'DESC');

        if (!empty($products)) {
            foreach ($products as $product) {
                // Determine last modified date (fallback to today if no date)
                $lastMod = !empty($product['updated_at']) 
                    ? date('Y-m-d', strtotime($product['updated_at'])) 
                    : date('Y-m-d');
                
                $xml .= "    <url>\n";
                $xml .= "        <loc>" . htmlspecialchars($baseUrl . '/product/' . $product['slug']) . "</loc>\n";
                $xml .= "        <lastmod>{$lastMod}</lastmod>\n";
                $xml .= "        <priority>0.8</priority>\n";
                $xml .= "    </url>\n";
            }
        }

        $xml .= '</urlset>';

        // Output raw XML payload
        echo $xml;
        exit;
    }
}
