<?php

namespace App\Console;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Adapter extends CLI
{
	protected function setup(Options $options): void
	{
		$options->setHelp('Nebula console application');

		$options->registerCommand("serve", "Start development server", "serve");
		$options->registerOption('generate-key', 'Generate secure application key');

		$options->registerOption('version', 'Print version', 'v');
	}

	protected function main(Options $options): void
	{
		match ($options->getCmd()) {
			"serve" => $this->serve($options->getArgs()),
			"generate-key" => $this->generate_key(),
			default => ''
		};
		foreach ($options->getOpt() as $opt => $val) {
			match ($opt) {
				"version" => $this->version(),
			};
		}
		echo $options->help();
	}

	private function version(): void
	{
		$this->info(config("application.version"));
		exit;
	}

	private function generate_key(): void
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
