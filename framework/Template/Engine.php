<?php

namespace Nebula\Framework\Template;

use Exception;

class Engine
{
	public function render(string $path, array $data = []): string
	{
		if (!file_exists($path)) throw new Exception("Template path not found");
		extract($data);
		ob_start();
		require $path;
		return ob_get_clean();
	}
}
