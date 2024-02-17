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

        extract($data);

        ob_start();
        require $path;
        return ob_get_clean();
    }
}
