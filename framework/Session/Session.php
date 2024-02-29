<?php

namespace Nebula\Framework\Session;

use Nebula\Framework\Traits\Singleton;

class Session
{
    use Singleton;

    private $data = [];

    public function __construct()
    {
        $this->data = $this->getAll();
    }

    /**
     * Set a session value
     * @param string $key session key
     */
    public function get(string $key): mixed
    {
        @session_start();
        session_write_close();
        $this->data = $_SESSION;
        return $this->data[$key] ?? null;
    }

    /**
     * Set a session key/value
     * @param string $key session key
     * @param mixed $value session value
     */
    public function set(string $key, mixed $value): void
    {
        @session_start();
        $this->data[$key] = $value;
        $_SESSION = $this->data;
        session_write_close();
    }

    /**
     * Delete a session key
     * @param string $key session key
     */
    public function delete(string $key): void
    {
        @session_start();
        unset($this->data[$key]);
        $_SESSION = $this->data;
        session_write_close();
    }

    /**
     * Checks existence of session key
     * @param string $key session key
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get all session variables
     */
    public function getAll(): array
    {
        @session_start();
        session_write_close();
        return $_SESSION;
    }

    /**
     * Destroy a session
     */
    public function destroy(): void
    {
        @session_start();
        $_SESSION = $this->data = [];
        session_destroy();
        session_write_close();
    }
}
