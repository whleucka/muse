<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    protected array $error_messages = [
        "required" => "%s is a required field.",
        "unique" => "%s must be unique."
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
    function render(string $path, array $data = []): string
    {
        $data["request_errors"] = fn(string $field) => isset($this->request_errors[$field])
            ? template("components/request_errors.php", ["errors" => $this->request_errors[$field]])
            : "";
        $data["escape"] = fn(string $key) => $this->escapeRequest($key);

        return template($path, $data, true);
    }

    /**
     * Sanitize value for template
     */
    public function escapeRequest(string $key): mixed
    {
        return htmlspecialchars($this->request($key), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function validateRequest(array $ruleset): array
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
                    "array" => is_array($value),
                    "string" => is_string($value),
                    "numeric" => is_numeric($value),
                    "int" => is_int($value),
                    "float" => is_float($value),
                    "unique" => $this->is_unique($mod, $field, $value),
                };
                if (!$result && isset($this->error_messages[$rule])) {
                    $this->addRequestError($field, $this->error_messages[$rule]);
                }
            }
        }
        return count($this->request_errors) === 0 ? $data : [];
    }

    private function is_unique(string $mod, string $field, string $value): bool
    {
        return !db()->fetch("SELECT * FROM $mod WHERE $field = ?", $value);
    }

    protected function addRequestError(string $field, string $message): void
    {
        $this->request_errors[$field][] = sprintf($message, ucfirst($field));
    }

    public function request(?string $key = null, mixed $default = null): mixed
    {
        if (!$key) {
            return $this->request;
        }
        return $this->request->get($key, $default);
    }
}
