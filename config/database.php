<?php

return [
	"enabled" => env("DB_ENABLED", false),
	"type" => env("DB_TYPE"),
	"path" => env("DB_PATH"),
	"dbname" => env("DB_NAME"),
	"username" => env("DB_USERNAME"),
	"password" => env("DB_PASSWORD"),
	"host" => env("DB_HOST"),
	"port" => env("DB_PORT"),
	"charset" => env("DB_CHARSET"),
	"options" => [],
];
