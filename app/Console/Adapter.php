<?php

namespace App\Console;

use Nebula\Framework\Console\Migrations;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Adapter extends CLI
{
	protected function setup(Options $options): void
	{
		$options->setHelp('Nebula console application');

		$options->registerCommand("serve", "Start development server", "serve");
		$options->registerCommand("generate-key", "Generate secure application key", "generate-key");
		$options->registerCommand("migrate-fresh", "Migrate fresh database", "migrate-fresh");

		$options->registerOption('version', 'Print version', 'v');
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
		$migrations = new Migrations();
		$migs = $migrations->getMigrations();
		rsort($migs);
		foreach ($migs as $mig) {
			$result = $migrations->migrationDown($mig);
			if ($result) {
				$this->success("Migration down: " . $mig->down());
			}
		}
		rsort($migs);
		foreach ($migs as $mig) {
			$result = $migrations->migrationUp($mig);
			if ($result) {
				$this->success("Migration up: " . $mig->up());
			}
		}
		exit;
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
