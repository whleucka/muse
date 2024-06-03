<?php

namespace App\Console;

use Nebula\Framework\Console\Migrations;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use App\Muse\Music\Scan;
use App\Models\Track;
use App\Models\TrackMeta;
use PDO;

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
            "Generate secure application key"
        );
        $options->registerCommand(
            "migrations",
            "See migration list and statuses"
        );
        $options->registerCommand(
            "migrate-fresh",
            "WARNING: Drops database and migrates fresh database"
        );
        $options->registerCommand("migrate-up", "Run migration UP method");
        $options->registerCommand("migrate-down", "Run migration DOWN method");

        $options->registerOption("version", "Print version", "v");

        $options->registerCommand(
            "music-scan",
            "Scan and sync music files to the database",
        );
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
            "migrations" => $this->migrateList(),
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

    private function migrateList(): void
    {
        $this->info(" Migrations:");
        sleep(1);
        $migrations = $this->migrations->mapMigrations();
        foreach ($migrations as $migration) {
            $msg = sprintf(
                "%s ... %s\n",
                $migration["name"],
                $migration["status"]
            );
            switch ($migration["status"]) {
                case "pending":
                    $this->notice($msg);
                    break;
                case "complete":
                    $this->success($msg);
                    break;
                case "failure":
                    $this->alert($msg);
                    break;
            }
        }
        $this->notice(" Complete!");
        exit();
    }

    private function migrateFresh(): void
    {
        $this->info(" Creating a new database...");
        sleep(1);
        $this->migrations->refreshDatabase();
        $migrations = $this->migrations->mapMigrations();
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
            $hash = $migration["hash"];
            $result = $this->migrations->up($class, $hash);
            if ($result) {
                $this->success(" Migration up: " . $name);
            } elseif (is_null($result)) {
                $this->info(" Migration already exists: " . $name);
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
            $hash = $migration["hash"];
            $result = $this->migrations->down($class, $hash);
            if ($result) {
                $this->success(" Migration down: " . $name);
            } elseif (is_null($result)) {
                $this->info(" Migration already exists: " . $name);
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

    private function removeOrphans(): int
    {
        $tracks = Track::all();
        $removed = 0;
        foreach ($tracks as $track) {
            if (!file_exists($track->name)) {
                $removed++;
                $track->delete();
            }
        }
        return $removed;
    }

    private function scanNew(array $files)
    {
        $new = 0;
        foreach ($files as $file) {
            $exists = Track::findByAttribute("name", $file);
            if (!$exists) {
                $new++;
                Track::new(["name" => $file]);
            }
        }
        return $new;
    }

    private function synchronize(array $files): void
    {
        $this->info(" Now scanning music directory...");
        sleep(1);
        $file_count = count($files);
        $new = $this->scanNew($files);
        $removed = $this->removeOrphans();
        $this->info(" File count: $file_count");
        $this->info(" New files: $new");
        $this->info(" Removed orphans: $removed");
        $this->success(" Scan complete.");
    }

    private function id3(): void
    {
        $this->info(" Updating track metadata...");
        sleep(1);
        $tracks = db()->run("SELECT id FROM tracks WHERE NOT EXISTS (SELECT * FROM track_meta WHERE track_id = tracks.id)")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tracks as $id) {
            $track = new Track($id);
            $tags = $track->analyze();
            $comments = $tags["comments_html"] ?? [];
            TrackMeta::new([
                "track_id" => $track->id,
                "cover" => $track->cover(),
                "filesize" => intval($tags["filesize"] ?? 0),
                "bitrate" => intval($tags["bitrate"] ?? 0),
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
