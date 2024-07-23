<?php

/**
 * Useful application helper functions
 * NOTE: do not add namespace
 */

use App\Application;
use App\Http\Kernel as HttpKernel;
use App\Console\Kernel as ConsoleKernel;
use App\Models\User;
use Lunar\Interface\DB;
use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Session\Session;
use Nebula\Framework\Template\Engine;

function app(): Application
{
    $kernel = HttpKernel::getInstance();
    return Application::getInstance($kernel);
}

function user(): ?User
{
    return Auth::user();
}

function user_ip()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    return $ip;
}

function curlRequest($url, $method = 'GET', $data = [], $headers = []) {
    $ch = curl_init();

    // Set URL
    if ($method === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
    }
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set request method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // If POST request, set POST fields
    if ($method === 'POST' && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    // Set headers
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    // Return the response as a string instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set cURL options to capture detailed error information
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    // Get HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $decodedResponse = json_decode($response, true);

    // Return the decoded response along with the HTTP status code
    return [
        'http_code' => $httpCode,
        'response' => $decodedResponse,
    ];
}

function json(mixed $data)
{
    return json_encode($data, JSON_PRETTY_PRINT);
}

function console(): Application
{
    $kernel = ConsoleKernel::getInstance();
    return Application::getInstance($kernel);
}

function session(): Session
{
    return Session::getInstance();
}

/**
 * Get application PDO database wrapper
 */
function db(): ?DB
{
    return app()->database;
}

/**
 * Print a debug
 */
function dump(...$data)
{
    $debug = debug_backtrace()[0];
    $pre_style =
        "overflow-x: auto; font-size: 0.6rem; border-radius: 10px; padding: 10px; background: #133; color: azure; border: 3px dotted azure;";
    $scrollbar_style =
        "scrollbar-width: thin; scrollbar-color: #5EFFA1 #113333;";

    foreach ($data as $datum) {
        if (php_sapi_name() === "cli") {
            print_r($datum);
        } else {
            printf(
                "<pre style='%s %s'><div style='margin-bottom: 5px;'><strong style='color: #5effa1;'>DUMP</strong></div><div style='margin-bottom: 5px;'><strong>File:</strong> %s:%s</div><div style='margin-top: 10px;'>%s</div></pre>",
                $pre_style,
                $scrollbar_style,
                $debug["file"],
                $debug["line"],
                print_r($datum, true)
            );
        }
    }
}

/**
 * Print a debug and die
 */
function dd($data)
{
    dump($data);
    die();
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
 * @param bool $decode decode html entities
 */
function template(string $path, array $data = [], bool $decode = false): string
{
    $engine = Engine::getInstance();
    $template = config("path.templates");
    return $decode
        ? html_entity_decode($engine->render("$template/$path", $data))
        : $engine->render("$template/$path", $data);
}

/**
* Creates a HX-Location headers
*/
function location(array $options)
{
    if (empty($options)) return;
    $header =  sprintf("HX-Location:%s", json_encode($options));
    header($header);
}
