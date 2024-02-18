<?php

namespace App\Console;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Adapter extends CLI
{
	protected function setup(Options $options): void
	{
		$options->setHelp('Nebula console application');
		$options->registerOption('version', 'print version', 'v');
	}

	protected function main(Options $options): void
	{
		if (empty($options->getOpt())) {
			echo $options->help();
		}
		foreach ($options->getOpt() as $opt => $val) {
			match ($opt) {
				"version" => $this->info(config("application.version")),
			};
		}
	}
}
