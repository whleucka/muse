<?php

/**
 * Useful application helper functions
 * NOTE: do not add namespace
 */

use Lunar\Connection\MySQL;
use Lunar\Connection\SQLite;
use Lunar\Interface\DB;
use Nebula\Framework\Template\Engine;
use Nebula\Framework\Session\Session;

/**
 * Print a debug
 */
function dump($data)
{
    $debug = debug_backtrace()[0];
    $pre_style = "overflow-x: auto; font-size: 0.6rem; border-radius: 10px; padding: 10px; background: #133; color: azure; border: 3px dotted azure;";
    $scrollbar_style = "scrollbar-width: thin; scrollbar-color: #5EFFA1 #113333;";

    printf("<pre style='%s %s'><div style='margin-bottom: 5px;'><strong style='color: #5effa1;'>DUMP</strong></div><div style='margin-bottom: 5px;'><strong>File:</strong> %s:%s</div><div style='margin-top: 10px;'>%s</div></pre>", $pre_style, $scrollbar_style, $debug["file"], $debug["line"], print_r($data, true));
}

/**
 * Print a debug and die
 */
function dd($data)
{
    dump($data);
    die();
}

function session(): Session
{
    return new Session;
}

/**
 * Get application PDO database wrapper
 */
function db(): ?DB
{
    $config = config("database");
    if (!$config["enabled"]) {
        return null;
    }
    return match ($config["type"]) {
        "mysql" => new MySQL(
            $config["dbname"],
            $config["username"],
            $config["password"],
            $config["host"],
            $config["port"],
            $config["charset"],
            $config["options"]
        ),
        "sqlite" => new SQLite($config["path"], $config["options"]),
        default => throw new Exception("unknown database driver"),
    };
}

/**
 * Get application environment setting
 */
function env(string $name, $default = "")
{
    if (isset($_ENV[$name])) {
        $lower = strtolower($_ENV[$name]);
        return match ($lower) {
            "true" => true,
            "false" => false,
            default => $_ENV[$name],
        };
        return $_ENV[$name];
    }
    return $default;
}

/**
 * Get application configuration settings
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
 * Generate content using template
 * @param string $path template path
 * @param array<string,mixed> $data template data
 */
function template(string $path, array $data = []): string
{
    $eng = new Engine();
    $template = config("path.templates");
    return $eng->render("$template/$path", $data);
}

/**
 * Extends a template layout
 * @param string $path template path to extend
 * @param array<string,mixed> $data template data
 */
function extend(string $path, array $data)
{
    return html_entity_decode(template($path, $data));
}

/**
 * Get a CSRF input
 */
function csrf(): string
{
    $token = session()->get("csrf_token");
    return template("components/csrf.php", ["token" => $token]);
}

/**
 * Get a token string
 */
function generateToken(): string
{
    return bin2hex(random_bytes(32));
}

function isEncrypted(mixed $value)
{
	return strpos($value, '|crypt|') !== false;
}

function encrypt(mixed $value)
{
    $app_key = config("security.app_key");
    $encrypted = openssl_encrypt($value, 'AES-256-CBC', $app_key, 0, substr($app_key, 0, 16));
    // Add a marker to indicate that the cookie is encrypted
    $encrypted .= '|crypt|';
    return $encrypted;
}

function decrypt(mixed $value)
{
    $app_key = config("security.app_key");
    return openssl_decrypt(str_replace('|crypt|', '', $value), 'AES-256-CBC', $app_key, 0, substr($app_key, 0, 16));
}
