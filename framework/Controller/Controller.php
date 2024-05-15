<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    protected array $error_messages = [
        "array" => "{title} must be an array",
        "email" => "Must be a valid email address",
        "float" => "{title} must be a float",
        "int" => "{title} must be an integer",
        "match" => "Must match {arg}",
        "max" => "Greater than maximum allowed: {arg}",
        "min" => "Less than minimum allowed: {arg}",
        "maxlength" => "Greater than maximum length: {arg}",
        "minlength" => "Less than minimum length: {arg}",
        "numeric" => "{title} must be numeric",
        "required" => "{title} is required",
        "string" => "{title} must be a string",
        "unique" => "{title} must be unique",
        "symbol" => "{title} must contain a special character",
        "not_empty" => "{title} cannot be empty",
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
        $data["request_errors"] = fn(string $field) => $this->getRequestError(
            $field
        );
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

    public function getRequestError(string $field): ?string
    {
        if (!isset($this->request_errors[$field])) {
            return null;
        }
        return template("components/request_errors.php", [
            "errors" => $this->request_errors[$field],
        ]);
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

    /**
     * Return request super global
     */
    public function getRequest(): array
    {
        $request = [];
        $exclude = ["PHPSESSID", "csrf_token"];
        foreach ($this->request()->request as $key => $value) {
            if (!in_array($key, $exclude)) {
                $request[$key] = $value;
            }
        }
        return $request;
    }

    private function ruleArg(mixed $rule): array
    {
        $raw = explode("|", $rule);
        $rule = $raw[0];
        $arg = isset($raw[1]) ? $raw[1] : "";
        return [strtolower($rule), $arg];
    }

    /**
     * Returns a validated request array
     * @param array $ruleset
     * @return bool|array false if validation fails
     */
    public function validateRequest(array $ruleset): bool|array
    {
        $request = $this->getRequest();
        foreach ($ruleset as $field => $rules) {
            $valid = true;
            $value = isset($request[$field]) ? $request[$field] : null;
            if ($value === "NULL") {
                $value = null;
            }
            $is_required = in_array("required", $rules);
            foreach ($rules as $idx => $rule) {
                if ($idx === "custom" && is_array($rule)) {
                    $method = $rule["method"] ?? false;
                    $message = $rule["message"] ?? "*message not set*";
                    $valid &= $method($field, $value);
                    if (!$valid) {
                        $this->addRequestError($field, null, $message);
                    }
                } else {
                    [$rule, $arg] = $this->ruleArg($rule);
                    if (
                        (trim($value) === "" ||
                            is_null($value) ||
                            $value === "NULL") &&
                        !$is_required
                    ) {
                        $valid &= true;
                    } else {
                        $valid &= match ($rule) {
                            "not_empty" => trim($value) !== "",
                            "array" => is_array($value),
                            "email" => filter_var(
                                $value,
                                FILTER_VALIDATE_EMAIL
                            ) !== false,
                            "match" => $value == $request[$arg],
                            "max" => intval($value) <= intval($arg),
                            "min" => intval($value) >= intval($arg),
                            "maxlength" => strlen($value) <= intval($arg),
                            "minlength" => strlen($value) >= intval($arg),
                            "numeric" => is_numeric($value),
                            "required" => trim($value) !== "" &&
                                $value !== "NULL",
                            "string" => is_string($value),
                            "unique" => !db()->fetch(
                                "SELECT * FROM $arg WHERE $field = ?",
                                $value
                            ),
                            "symbol" => preg_match("/[^\w\s]/", $value),
                            default => false,
                        };
                    }
                }
                if (
                    !$valid &&
                    is_string($rule) &&
                    isset($this->error_messages[$rule])
                ) {
                    $message = $this->error_messages[$rule];
                    $this->addRequestError($field, $arg, $message);
                }
            }
        }
        return empty($this->request_errors) ? $request : false;
    }

    protected function replaceErrorTitle(string $field, string $message): string
    {
        return str_replace("{title}", ucfirst($field), $message);
    }

    protected function replaceErrorArg(?string $arg, string $message): string
    {
        return str_replace("{arg}", $arg, $message);
    }

    public function addRequestError(
        string $field,
        ?string $arg,
        string $message
    ): void {
        $message = $this->replaceErrorTitle($field, $message);
        $message = $this->replaceErrorArg($arg, $message);
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
