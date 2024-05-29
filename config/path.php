<?php

$root = __DIR__ . "/../";
$app = $root . "app/";

return [
	"root" => $root,
	"bin" => $root . "bin",
	"templates" => $root . "templates",
	"controllers" => $app . "Controllers",
	"migrations" => $root . "migrations",
	"middleware" => $app . "Middleware",
	"public" => $root . "public",
	"storage" => $root . "storage",
	"modules" => $app . "Modules",
	"public_covers" => "/storage/covers",
	"storage_covers" => $root . "storage/covers",
	"storage_transcode" => $root . "storage/transcode",
];
