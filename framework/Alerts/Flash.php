<?php

namespace Nebula\Framework\Alerts;

class Flash
{
    private static array $messages = [];

    public static function add(string $type, string $message): void
    {
        self::$messages[$type][] = $message;
    }

    public static function get(): array
    {
        return self::$messages;
    }
}
