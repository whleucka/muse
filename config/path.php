<?php

$root = __DIR__ . "/../";
$app = $root . "app/";

return [
	"root" => $root,
	"templates" => $root . "templates",
	"controllers" => $app . "Controllers",
	"migrations" => $root . "migrations",
	"middleware" => $app . "Middleware",
	"public" => $root . "public",
];
