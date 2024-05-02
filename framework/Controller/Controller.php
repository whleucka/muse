<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    protected array $error_messages = [
        "array" => "must be an array",
        "email" => "must be a valid email address",
        "float" => "must be a float",
        "int" => "must be an integer",
        "match" => "must match",
        "max" => "greater than maximum allowed",
        "min" => "less than minimum allowed",
        "maxlength" => "greater than maximum length",
        "minlength" => "less than minimum length",
        "numeric" => "must be numeric",
        "required" => "required field",
        "string" => "must be a string",
        "unique" => "must be unique",
        "symbol" => "must contain a special character",
    ];
    protected array $request_errors = [];

    public function __construct(protected Request $request)
    {
        $this->bootstrap();
    }

    protected function bootstrap(): void
    {
    }

    /**
     * Render template response
     * @param string $path template path
     * @param array<string,mixed> $data template data
     */
    public function render(string $path, array $data = []): string
    {
        // Template functions
        $data["request_errors"] = fn(
            string $field,
            string $title = ""
        ) => $this->getRequestError($field, $title);
        $data["has_error"] = fn(string $field) => $this->hasRequestError(
            $field
        );
        $data["escape"] = fn(string $key) => $this->escapeRequest($key);
        $data["title"] = config("application.name");
        $data["route"] = function ($name) {
            $router = app()->router();
            $route = $router->findRouteByName($name);
            return $route ? $route->getPath() : "";
        };

        return template($path, $data, true);
    }

    public function getRequestError(string $field, string $title = ""): ?string
    {
        if (!$title) {
            $title = ucfirst($field);
        }
        return isset($this->request_errors[$field])
            ? template("components/request_errors.php", [
                "errors" => $this->request_errors[$field],
                "title" => $title,
            ])
            : "";
    }

    /**
     * Sanitize value for template
     */
    private function escapeRequest(string $key): mixed
    {
        return htmlspecialchars(
            $this->request($key) ?? "",
            ENT_QUOTES | ENT_HTML5,
            "UTF-8"
        );
    }

    public function validateRequest(array $ruleset): array
    {
        $data = [];
        foreach ($ruleset as $field => $rules) {
            $data[$field] = $value = $this->request($field);
            $is_required = in_array("required", $rules);
            foreach ($rules as $rule) {
                $raw = explode("|", $rule);
                $rule = $raw[0];
                $arg_1 = $raw[1] ?? null;
                $rule = strtolower($rule);
                if (is_null($value) && !$is_required) {
                    $result = true;
                } else {
                    $result = match ($rule) {
                        "non_empty_string" => trim($value) !== "",
                        "array" => is_array($value),
                        "email" => filter_var($value, FILTER_VALIDATE_EMAIL),
                        "match" => $value === $this->request($arg_1),
                        "max" => intval($value) <= intval($arg_1),
                        "min" => intval($value) >= intval($arg_1),
                        "maxlength" => strlen($value) <= intval($arg_1),
                        "minlength" => strlen($value) >= intval($arg_1),
                        "numeric" => is_numeric($value),
                        "required" => $value && trim($value) !== "",
                        "string" => is_string($value),
                        "unique" => !db()->fetch(
                            "SELECT * FROM $arg_1 WHERE $field = ?",
                            $value
                        ),
                        "symbol" => preg_match("/[^\w\s]/", $value),
                        default => false,
                    };
                }
                if (!$result && isset($this->error_messages[$rule])) {
                    $this->addRequestError(
                        $field,
                        $this->error_messages[$rule]
                    );
                }
            }
        }
        return count($this->request_errors) === 0 ? $data : [];
    }

    public function addRequestError(string $field, string $message): void
    {
        $this->request_errors[$field][] = $message;
    }

    public function hasRequestError(string $field): bool
    {
        return isset($this->request_errors[$field]);
    }

    protected function request(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        if (is_null($key)) {
            return $this->request;
        }
        return $this->request->get($key, $default);
    }
}
