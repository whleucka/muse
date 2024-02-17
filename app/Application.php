<?php

namespace App;

use Exception;
use Lunar\Connection\MySQL;
use Lunar\Connection\SQLite;
use Lunar\Interface\DB;
use Nebula\Framework\Traits\Singleton;
use Nebula\Framework\System\Interfaces\Kernel;

class Application
{
    use Singleton;

    public ?DB $database;

    public function __construct(private Kernel $kernel)
    {
        $this->kernel->main();
        $this->database = $this->initDatabase();
    }

    public function run(): void
    {
        $this->kernel->response();
    }

    protected function initDatabase(): ?DB
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
}
