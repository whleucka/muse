<?php

namespace Nebula\Framework\Template;

use Exception;
use Nebula\Framework\Traits\Singleton;

class Engine
{
    use Singleton;

    /**
     * @param string $path template path
     * @param array<string,mixed> $data
     */
    public function render(string $path, array $data = []): string
    {
        if (!file_exists($path)) {
            throw new Exception("Template path not found");
        }

        // Adds a csrf token to a hidden input for a form
        $data["csrf"] = function() {
            $token = session()->get("csrf_token");
            return template("components/csrf.php", ["token" => $token]);
        };

        // You can output unsanitized strings with raw
        $data["raw"] = fn ($value) => html_entity_decode($data[$value]);

        $sanitized = array_map(fn ($value) => $this->sanitize($value), $data);
        extract($sanitized);

        ob_start();
        require $path;
        return ob_get_clean();
    }

    /**
     * Sanitize value for template
     */
    public function sanitize(mixed $value): mixed
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $value;
    }
}
