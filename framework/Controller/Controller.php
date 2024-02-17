<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    protected array $error_messages = [
        "required" => "required field",
        "unique" => "must be unique",
        "match" => "must match",
    ];
    protected array $request_errors = [];

    public function __construct(protected Request $request)
    {
    }

    /**
     * Render template response
     * @param string $path template path
     * @param array<string,mixed> $data template data
     */
    protected function render(string $path, array $data = []): string
    {
        $data["request_errors"] = fn(string $field, string $title = '') => $this->getRequestErrors($field, $title);
        $data["escape"] = fn(string $key) => $this->escapeRequest($key);

        return template($path, $data, true);
    }

    private function getRequestErrors(string $field, string $title = ''): ?string
    {
        if (!$title) $title = ucfirst($field);
        return isset($this->request_errors[$field])
            ? template("components/request_errors.php", ["errors" => $this->request_errors[$field], "title" => $title])
            : "";
    }

    /**
     * Sanitize value for template
     */
    private function escapeRequest(string $key): mixed
    {
        return htmlspecialchars($this->request($key), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function validateRequest(array $ruleset): array
    {
        $data = [];
        foreach ($ruleset as $field => $rules) {
            $data[$field] = $this->request($field);
            foreach ($rules as $rule) {
                $rules = explode("|", $rule);
                $rule = $rules[0];
                $mod = $rules[1] ?? null;
                $rule = strtolower($rule);
                $value = $this->request($field);
                $result = match ($rule) {
                    "required" => $value && trim($value) !== '',
                    "match" => $value === $mod,
                    "array" => is_array($value),
                    "string" => is_string($value),
                    "numeric" => is_numeric($value),
                    "int" => is_int($value),
                    "float" => is_float($value),
                    "unique" => !db()->fetch("SELECT * FROM $mod WHERE $field = ?", $value),
                };
                if (!$result && isset($this->error_messages[$rule])) {
                    $this->addRequestError($field, $this->error_messages[$rule]);
                }
            }
        }
        return count($this->request_errors) === 0 ? $data : [];
    }

    protected function addRequestError(string $field, string $message): void
    {
        $this->request_errors[$field][] = $message;
    }

    protected function request(?string $key = null, mixed $default = null): mixed
    {
        if (!$key) {
            return $this->request;
        }
        return $this->request->get($key, $default);
    }
}
