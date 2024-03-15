<?php

namespace App\Console;

use App\Models\Track;
use App\Models\TrackMeta;
use App\Muse\Music\Scan;
use Nebula\Framework\Console\Migrations;
use PDO;
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
        $options->registerCommand(
            "music-scan",
            "Scan and sync music files to the database",
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
            "music-scan" => $this->musicScan($options->getArgs()),
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
            fn ($mig) => $mig["name"] === basename($migration_file[0])
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
        uasort($migrations, fn ($a, $b) => $a["name"] <=> $b["name"]);
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

    private function musicScan(array $paths): void
    {
        $files = [];
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                $this->error(" Path does not exist");
            }
            $scanner = new Scan();
            $files = [...$files, ...$scanner->find($path)];
        }
        $this->synchronize($files);
        $this->id3();
        exit();
    }

    private function synchronize(array $files): void
    {
        $this->info(" Now scanning music directory...");
        sleep(1);
        $file_count = count($files);
        $new = 0;
        foreach ($files as $file) {
            $exists = db()->fetch("SELECT * FROM tracks WHERE name = ?", $file);
            if (!$exists) {
                Track::new(["name" => $file]);
                $new++;
            }
        }
        $this->info(" File count: $file_count");
        $this->info(" New files: $new");
        $this->success(" Scan complete.");
    }

    private function id3(): void
    {
        $this->info(" Updating track metadata...");
        sleep(1);
        $tracks = db()->run("SELECT id FROM tracks")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tracks as $id) {
            $track = new Track($id);
            $tags = $track->analyze();
            $comments = $tags["comments_html"];
            TrackMeta::new([
                "track_id" => $track->id,
                "cover" => $track->cover(),
                "filesize" => intval($tags["filesize"]),
                "bitrate" => intval($tags["bitrate"]),
                "mime_type" => $tags["mime_type"] ?? "unknown",
                "playtime_string" => $tags["playtime_string"] ?? "0:00",
                "playtime_seconds" => round($tags["playtime_seconds"] ?? 0),
                "track_number" => isset($comments["track_number"]) ? intval($comments["track_number"][0]) : null,
                "title" => isset($comments["title"]) ? $comments["title"][0] : "No Title",
                "artist" => isset($comments["artist"]) ? $comments["artist"][0] : "No Artist",
                "album" => isset($comments["album"]) ? $comments["album"][0] : "No Album",
                "genre" => isset($comments["genre"]) ? implode(", ", $comments["genre"]) : null,
                "year" => isset($comments["year"]) ? intval($comments["year"][0]) : null,
            ]);
        }
        $this->success(" Analysis complete.");
    }
}
