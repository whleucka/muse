<?php

/**
 * Useful application helper functions
 */

use Nebula\Framework\Template\Engine;

/**
 * Print a debug
 */
function dump($data)
{
	printf("<pre>%s</pre>", print_r($data, true));
}

/**
 * Print a debug and die
 */
function dd($data)
{
	dump($data);die;
}

/**
* Return application configuration settings
* @param string $name name of the configuration attribute
* @return mixed configuration settings
*/
function config(string $name): mixed
{
	// There could be a warning if $attribute
	// is not set, so let's silence it
	@[$file, $attribute] = explode(".", $name);
	$config_path = __DIR__ . "/../../config/$file.php";
	if (file_exists($config_path)) {
		$config = require $config_path;
		return $attribute && key_exists($attribute, $config)
			? $config[$attribute]
			: $config;
	}
	return false;
}

/**
* Returns the content of a template
* @param string $path template path of template
* @param array $data variables for template replacements
*/
function template(string $path, array $data = []): string
{
	$eng = new Engine;
	$template = config("path.templates");
	return $eng->render("$template/$path", $data);
}
