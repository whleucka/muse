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

		$files = array();

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
		$migrations = $this->getMigrationFiles($migration_path);
		return array_map(fn($path) => require($path), $migrations);
	}

	public function migrationUp(Migration $migration)
	{
		return db()->query($migration->up());
	}

	public function migrationDown(Migration $migration)
	{
		return db()->query($migration->down());
	}
}
