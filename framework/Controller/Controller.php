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
        "max" => "greater than max",
        "min" => "less than min",
        "maxlength" => "greater than max length",
        "minlength" => "less than min length",
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
    protected function render(string $path, array $data = []): string
    {
        // Template functions
        $data["request_errors"] = fn (
            string $field,
            string $title = ""
        ) => $this->getRequestErrors($field, $title);
        $data["has_error"] = fn (string $field) => $this->hasRequestError(
            $field
        );
        $data["escape"] = fn (string $key) => $this->escapeRequest($key);
        $data["title"] = config("application.name");
        $data["route"] = function ($name) {
            $router = app()->router();
            $route = $router->findRouteByName($name);
            return $route ? $route->getPath() : '';
        };

        return template($path, $data, true);
    }

    private function getRequestErrors(
        string $field,
        string $title = ""
    ): ?string {
        if (!$title) {
            $title = ucfirst($field);
        }
        return isset($this->request_errors[$field])
            ? template("components/request_errors.php", [
                "errors" => $this->request_errors[$field],
                "title" => $title,
            ]) : "";
    }

    /**
     * Sanitize value for template
     */
    private function escapeRequest(string $key): mixed
    {
        return htmlspecialchars(
            $this->request($key) ?? '',
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
                        // Cannot be empty string
                        "non_empty_string" => trim($value) !== '',
                        // value is an array
                        "array" => is_array($value),
                        // value is an email address
                        "email" => filter_var($value, FILTER_VALIDATE_EMAIL),
                        // value is a float
                        "float" => is_float($value),
                        // value is an integer
                        "int" => is_int($value),
                        // value must match other request item
                        "match" => $value === $this->request($arg_1),
                        // value must be less than or equal to max
                        "max" => intval($value) <= intval($arg_1),
                        // value must be larger than or equal to min
                        "min" => intval($value) >= intval($arg_1),
                        // length must be less than or equal to maxlength
                        "maxlength" => strlen($value) <= intval($arg_1),
                        // length must be larger than or equal to minlength
                        "minlength" => strlen($value) >= intval($arg_1),
                        // value is numeric
                        "numeric" => is_numeric($value),
                        // value is required
                        "required" => $value && trim($value) !== "",
                        // vlaue is a string
                        "string" => is_string($value),
                        // value is unique in db
                        "unique" => !db()->fetch(
                            "SELECT * FROM $arg_1 WHERE $field = ?",
                            $value
                        ),
                        // value contains a symbol
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

    protected function addRequestError(string $field, string $message): void
    {
        $this->request_errors[$field][] = $message;
    }

    protected function hasRequestError(string $field): bool
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
