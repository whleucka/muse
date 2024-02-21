<?php

namespace App\Console;

use Nebula\Framework\Console\Migrations;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Adapter extends CLI
{
	private Migrations $migrations;

	public function __construct()
	{
		$this->migrations = new Migrations();
		parent::__construct();
	}

	protected function setup(Options $options): void
	{
		$options->setHelp('Nebula console application');

		$options->registerCommand("serve", "Start development server", "serve");
		$options->registerCommand("generate-key", "Generate secure application key", "generate-key");
		$options->registerCommand("migrate-fresh", "Migrate fresh database", "migrate-fresh");

		$options->registerOption('version', 'Print version', 'v');
	}

	private function mapMigrations(): array
	{
		$migs = $this->migrations->getMigrations();
		return array_map(fn($path) => ["name" => basename($path), "class" => require($path)], $migs);
	}

	protected function main(Options $options): void
	{
		match ($options->getCmd()) {
			"serve" => $this->serve($options->getArgs()),
			"generate-key" => $this->generateKey(),
			"migrate-fresh" => $this->migrateFresh(),
			default => ''
		};
		foreach ($options->getOpt() as $opt => $val) {
			match ($opt) {
				"version" => $this->version(),
			};
		}
		echo $options->help();
	}

	private function migrateFresh(): void
	{
		$migrations = $this->mapMigrations();
		$this->dropDatabase();
		uasort($migrations, fn($a, $b) => $a["name"] <=> $b["name"]);
		$this->migrateUp($migrations);
		exit;
	}

	private function dropDatabase(): void
	{
		$db_name = config("database.dbname");
		db()->query(sprintf("DROP DATABASE %s", $db_name));
		db()->query(sprintf("CREATE DATABASE %s", $db_name));
		db()->query(sprintf("USE %s", $db_name));
	}

	private function migrateUp(array $migrations): void
	{
		foreach ($migrations as $migration) {
			$class = $migration["class"];
			$name = $migration["name"];
			$result = $this->migrations->migrationUp($class);
			if ($result) {
				$this->success("Migration up: " . $name);
			}
		}
	}

	private function migrateDown(array $migrations): void
	{
		foreach ($migrations as $migration) {
			$class = $migration["class"];
			$name = $migration["name"];
			$result = $this->migrations->migrationDown($class);
			if ($result) {
				$this->success("Migration down: " . $name);
			}
		}
	}

	private function version(): void
	{
		$this->info(config("application.version"));
		exit;
	}

	private function generateKey(): void
	{
		$unique = uniqid(more_entropy: true);
		$key = `echo -n $unique | openssl dgst -binary -sha256 | openssl base64`;
		$this->success("Application key: " . $key);
		exit;
	}

	private function serve(array $args): void
	{
		$bin_path = config("path.bin");
		$cmd = $bin_path . "/serve";
		if (count($args) === 2) {
			$cmd .= ' ' . $args[0] . ' ' . $args[1];
		}
		`$cmd`;
		exit;
	}
}
