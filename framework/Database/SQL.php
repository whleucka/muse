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
	public function file(string $migration_file): void
	{
		if (!file_exists($migration_file)) {
			throw new Exception("migration file doesn't exist");
		}
		$this->query = file_get_contents($migration_file);
	}
}
