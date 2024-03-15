<?php

namespace Nebula\Framework\Database;

use Closure;

class Schema
{
    /**
     * Run schema query
     */
    public static function run(Closure $callback): string
    {
        $sql = new SQL();
        $callback($sql);
        return $sql->query();
    }

    public static function skip()
    {
        return self::run(fn(SQL $sql) => $sql->raw("SELECT 1"));
    }
}
