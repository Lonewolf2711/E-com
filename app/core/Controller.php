<?php
/**
 * Base Controller
 * ───────────────
 * Abstract base class for all controllers.
 * Provides view rendering, redirects, JSON output, and input handling.
 */

class Controller
{
    /**
     * Render a view file with data.
     *
     * @param string $view View path relative to views/ (e.g., 'frontend/home')
     * @param array  $data Associative array of data to pass to the view
     */
    protected function render(string $view, array $data = []): void
    {
        // Extract data array into variables for use in view
        extract($data);

        // Build the full path to the view file
        $viewFile = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            $this->abort(404);
            return;
        }

        // Use output buffering for clean output
        ob_start();
        require_once $viewFile;
        $content = ob_get_clean();

        echo $content;
    }

    /**
     * Redirect to a URL with optional flash message.
     *
     * @param string      $url     Full URL or relative path
     * @param string|int  $message Flash message string, or HTTP status code (int)
     * @param string      $type    Flash message type (success, error, warning, info)
     */
    protected function redirect(string $url, string|int $message = '', string $type = 'info'): void
    {
        // If $message is an integer, treat it as the HTTP status code (backward compat)
        if (is_int($message)) {
            http_response_code($message);
        } else {
            http_response_code(302);
            if (!empty($message)) {
                Session::flash($type, $message);
            }
        }

        // Prepend base URL for relative paths (starting with '/')
        if (str_starts_with($url, '/')) {
            $url = url($url);
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Send JSON response.
     *
     * @param mixed $data Data to encode
     * @param int   $code HTTP status code
     */
    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Get a value from GET parameters.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Alias for input() — get a value from GET parameters.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return $this->input($key, $default);
    }

    /**
     * Get a value from POST parameters.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get all POST data.
     *
     * @return array
     */
    protected function postAll(): array
    {
        return $_POST;
    }

    /**
     * Get all GET data.
     *
     * @return array
     */
    protected function inputAll(): array
    {
        return $_GET;
    }

    /**
     * Abort with an HTTP error code.
     *
     * @param int $code HTTP status code
     */
    protected function abort(int $code): void
    {
        http_response_code($code);

        $errorViews = [
            404 => dirname(__DIR__, 2) . '/views/frontend/404.php',
            500 => dirname(__DIR__, 2) . '/views/frontend/500.php',
        ];

        if (isset($errorViews[$code]) && file_exists($errorViews[$code])) {
            require_once $errorViews[$code];
        } else {
            echo "<h1>Error {$code}</h1>";
        }

        exit;
    }

    /**
     * Validate that required fields are present in POST data.
     *
     * @param array $requiredFields List of required field names
     * @return array ['valid' => bool, 'errors' => array]
     */
    protected function validateRequired(array $requiredFields): array
    {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check if request is AJAX.
     *
     * @return bool
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request method is POST.
     *
     * @return bool
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
