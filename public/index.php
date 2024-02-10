<?php
/**
* NEBULA
*/
require_once __DIR__ . "/../vendor/autoload.php";

//use Nebula\Framework\Http\Kernel;

//new Kernel();

echo render("test/index.php", ["test" => "Hello, world"]);
