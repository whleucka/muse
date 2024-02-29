<?php

namespace Nebula\Framework\Traits;

trait Singleton
{
    protected static $instance;

    public static function getInstance(...$args)
    {
        if (static::$instance === null) {
            static::$instance = new static(...$args);
        }
        return static::$instance;
    }

    private function __construct()
    {
        // Prevent instantiation from outside
    }

    private function __clone(): void
    {
        // Prevent cloning
    }

    public function __wakeup(): void
    {
        // Prevent unserialization
    }
}
