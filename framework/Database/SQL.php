<?php

namespace Nebula\Framework\Database;

use Exception;

class SQL
{
    private string $query;

    /**
     * Return SQL query
     */
    public function query(): string
    {
        return $this->query;
    }

    /**
     * Run raw query
     */
    public function raw(string $sql): void
    {
        $this->query = $sql;
    }

    /**
     * Load SQL from file
     */
    public function file(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception("migration file doesn't exist");
        }
        $this->query = file_get_contents($path);
    }

    /**
     * Load SQL from migration path
     */
    public function migrationFile(string $path): void
    {
        $migration_path = config("path.migrations") . $path;
        $this->file($migration_path);
    }
}
