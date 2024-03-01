<?php

namespace App\Console;

use Nebula\Framework\Console\Migrations;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Adapter extends CLI
{
    private Migrations $migrations;

    public function __construct()
    {
        $this->migrations = new Migrations();
        parent::__construct();
    }

    protected function setup(Options $options): void
    {
        $options->setHelp("Nebula console application");

        $options->registerCommand("serve", "Start development server");
        $options->registerCommand(
            "generate-key",
            "Generate secure application key",
        );
        $options->registerCommand(
            "migrate-fresh",
            "WARNING: Drops database and migrates fresh database",
        );
        $options->registerCommand(
            "migrate-up",
            "Run migration UP method",
        );
        $options->registerCommand(
            "migrate-down",
            "Run migration DOWN method",
        );

        $options->registerOption("version", "Print version", "v");
    }

    protected function main(Options $options): void
    {
        foreach ($options->getOpt() as $opt => $val) {
            match ($opt) {
                "version" => $this->version(),
            };
        }
        match ($options->getCmd()) {
            "serve" => $this->serve($options->getArgs()),
            "generate-key" => $this->generateKey(),
            "migrate-fresh" => $this->migrateFresh(),
            "migrate-up" => $this->runMigration($options->getArgs(), "up"),
            "migrate-down" => $this->runMigration($options->getArgs(), "down"),
            default => "",
        };
        echo $options->help();
    }

    private function version(): void
    {
        $this->info(config("application.version"));
        exit();
    }

    private function generateKey(): void
    {
        $unique = uniqid(more_entropy: true);
        $key = `echo -n $unique | openssl dgst -binary -sha256 | openssl base64`;
        $this->success(" Application key: " . $key);
        $this->info(" Add this key to your .env file under APP_KEY");
        exit();
    }

    private function serve(array $args): void
    {
        $bin_path = config("path.bin");
        $cmd = $bin_path . "/serve";
        if (count($args) === 2) {
            $cmd .= " " . $args[0] . " " . $args[1];
        }
        `$cmd`;
        exit();
    }

    private function runMigration(
        array $migration_file,
        string $direction
    ): void {
        if (!isset($migration_file[0])) {
            $this->warning(" No migration file was given");
            exit();
        }
        $this->info(" Now running database migration...");
        sleep(1);
        $migration = array_filter(
            $this->migrations->mapMigrations(),
            fn($mig) => $mig["name"] === basename($migration_file[0])
        );
        if (!$migration) {
            $this->error(" Migration file doesn't exist");
            exit();
        }

        if ($direction === "up") {
            $this->migrateUp($migration);
        } elseif ($direction === "down") {
            $this->migrateDown($migration);
        } else {
            $this->error(" Unknown migration direction");
        }
        $this->notice(" Complete!");
        exit();
    }

    private function migrateFresh(): void
    {
        $this->info(" Creating a new database...");
        sleep(1);
        $migrations = $this->migrations->mapMigrations();
        $this->migrations->dropDatabase();
        uasort($migrations, fn($a, $b) => $a["name"] <=> $b["name"]);
        $this->migrateUp($migrations);
        $this->notice(" Complete!");
        exit();
    }

    private function migrateUp(array $migrations): void
    {
        foreach ($migrations as $migration) {
            $class = $migration["class"];
            $name = $migration["name"];
            $result = $this->migrations->up($class);
            if ($result) {
                $this->success(" Migration up: " . $name);
            } else {
                $this->error(" Migration error: " . $name);
            }
        }
    }

    private function migrateDown(array $migrations): void
    {
        foreach ($migrations as $migration) {
            $class = $migration["class"];
            $name = $migration["name"];
            $result = $this->migrations->down($class);
            if ($result) {
                $this->success(" Migration down: " . $name);
            } else {
                $this->error(" Migration error: " . $name);
            }
        }
    }
}
