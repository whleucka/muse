<?php

use Nebula\Framework\Template\Engine;

function dump($data)
{
	printf("<pre>%s</pre>", print_r($data, true));
}

function config(string $name): mixed
{
	[$file, $attribute] = explode(".", $name);
	$config_path = __DIR__ . "/../../config/$file.php";
	if (file_exists($config_path)) {
		$config = require $config_path;
		if ($attribute) {
			return $config[$attribute];
		}
		return $config;
	}
	return false;
}

function render(string $path, array $data = []): string
{
	$eng = new Engine;
	$template = config("path.templates");
	return $eng->render("$template/$path", $data);
}
