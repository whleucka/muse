<?php

namespace Nebula\Framework\Console;

use Nebula\Framework\Database\Interface\Migration;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Migrations
{
    public function getMigrationFiles(string $migration_path): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($migration_path),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $files = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === "php") {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function getMigrations(): array
    {
        $migration_path = config("path.migrations");
        return $this->getMigrationFiles($migration_path);
    }

    public function mapMigrations(): array
    {
        $migs = $this->getMigrations();
        return array_map(
            fn($path) => ["name" => basename($path), "class" => require $path],
            $migs
        );
    }

    public function dropDatabase(): void
    {
        $db_name = config("database.dbname");
        db()->query(sprintf("DROP DATABASE %s", $db_name));
        db()->query(sprintf("CREATE DATABASE %s", $db_name));
        db()->query(sprintf("USE %s", $db_name));
    }

    public function up(Migration $migration)
    {
        return db()->query($migration->up());
    }

    public function down(Migration $migration)
    {
        return db()->query($migration->down());
    }
}
