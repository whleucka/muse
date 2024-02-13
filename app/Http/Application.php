<?php

namespace App\Http;

class Application
{
	public function __construct(private Kernel $kernel)
	{
	}

	/**
	 * Invokes Kernel main method
	 * Provides application configuration
	 */
	public function run(): void
	{
		$response = $this->kernel->main($this->config());
		$response->send();
	}

	/**
	 * @return array compiled application configuration
	 */
	public function config(): array
	{
		return [
			"path" => config("path"),
			"database" => config("database"),
		];
	}
}
