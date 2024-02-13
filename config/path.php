<?php

$root = __DIR__ . "/../";
$app = $root . "app/";

return [
	"root" => $root,
	"templates" => $root . "templates",
	"controllers" => $app . "Controllers",
	"middleware" => $app . "Middleware",
	"public" => $root . "public",
];
