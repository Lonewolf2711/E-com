<?php
/**
 * Router Class
 * ────────────
 * Maps URL patterns to controller methods.
 * Supports dynamic parameters like {id} and {slug}.
 */

class Router
{
    /**
     * @var array Registered routes grouped by HTTP method
     */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'DELETE' => [],
    ];

    /**
     * Register a GET route.
     *
     * @param string $pattern URL pattern (e.g., '/product/{slug}')
     * @param mixed  $callback Controller@method string or callable
     * @param array  $middleware Middleware methods to run before handler
     */
    public function get(string $pattern, mixed $callback, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $callback, $middleware);
    }

    /**
     * Register a POST route.
     */
    public function post(string $pattern, mixed $callback, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $callback, $middleware);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $pattern, mixed $callback, array $middleware = []): void
    {
        $this->addRoute('DELETE', $pattern, $callback, $middleware);
    }

    /**
     * Add a route to our internal registry.
     */
    private function addRoute(string $method, string $pattern, mixed $callback, array $middleware): void
    {
        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'callback'   => $callback,
            'middleware'  => $middleware,
        ];
    }

    /**
     * Dispatch incoming request to matching route.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        // Check _method override for DELETE via POST
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Try to match a route
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            $params = $this->matchRoute($route['pattern'], $uri);

            if ($params !== false) {
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    if (is_string($mw) && str_contains($mw, '::')) {
                        [$class, $methodName] = explode('::', $mw);
                        $class::$methodName();
                    } elseif (is_callable($mw)) {
                        $mw();
                    }
                }

                // Call the controller method
                $this->callAction($route['callback'], $params);
                return;
            }
        }

        // No route matched — 404
        http_response_code(404);
        $errorPage = dirname(__DIR__, 2) . '/views/frontend/404.php';
        if (file_exists($errorPage)) {
            require_once $errorPage;
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
    }

    /**
     * Parse the request URI, stripping base path and query string.
     *
     * @return string Clean URI path
     */
    private function getUri(): string
    {
        $uri = $_GET['url'] ?? '';
        $uri = trim($uri, '/');
        return $uri;
    }

    /**
     * Try to match a URL pattern against a URI.
     *
     * @param string $pattern Route pattern (e.g., 'product/{slug}')
     * @param string $uri    Request URI (e.g., 'product/iphone-15')
     * @return array|false Extracted parameters, or false if no match
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        $pattern = trim($pattern, '/');

        // Exact match
        if ($pattern === $uri) {
            return [];
        }

        // Convert {param} to regex named groups
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            // Extract only named matches (string keys)
            $params = array_filter($matches, function ($key) {
                return is_string($key);
            }, ARRAY_FILTER_USE_KEY);

            return $params;
        }

        return false;
    }

    /**
     * Call a controller action.
     *
     * @param mixed $callback 'ControllerClass@method' string or callable
     * @param array $params   Route parameters
     */
    private function callAction(mixed $callback, array $params): void
    {
        if (is_string($callback) && str_contains($callback, '@')) {
            [$controllerName, $method] = explode('@', $callback);

            if (!class_exists($controllerName)) {
                throw new RuntimeException("Controller class '{$controllerName}' not found.");
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $method)) {
                throw new RuntimeException("Method '{$method}' not found in controller '{$controllerName}'.");
            }

            call_user_func_array([$controller, $method], array_values($params));
        } elseif (is_callable($callback)) {
            call_user_func_array($callback, array_values($params));
        } else {
            throw new RuntimeException('Invalid route callback.');
        }
    }
}
