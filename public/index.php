<?php

/**
 * NEBULA v0.0.1
 */
require_once __DIR__ . "/../vendor/autoload.php";

use App\Http\{Application, Kernel};

// Run instance of application
$app = new Application(new Kernel);
$app->run();
