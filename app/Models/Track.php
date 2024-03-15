<?php

namespace App\Models;

use getID3;
use getid3_lib;
use FFMpeg;
use Exception;

use Nebula\Framework\Model\Model;

class Track extends Model
{
    protected array $columns = [
        "id",
        "uuid",
        "name",
        "created_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("tracks", $key);
    }

    public function meta(): ?TrackMeta
    {
        return TrackMeta::findByAttribute("track_id", $this->id);
    }

    public function analyze(): array
    {
        $getID3 = new getID3;
        $tags = $getID3->analyze($this->name);
        getid3_lib::CopyTagsToComments($tags);
        return $tags;
    }

    public function cover(): ?string
    {
        $tags = $this->analyze();
        if (isset($tags["comments"]["picture"])) {
            $pictures = $tags["comments"]["picture"];
            foreach ($pictures as $picture) {
                if (isset($picture["picturetype"])) {
                    if (preg_match("/cover/i", $picture["picturetype"])) {
                        $cover_art = $this->extractAlbumArt($tags["filenamepath"], $picture);
                        if ($cover_art) {
                            return $cover_art;
                        }
                    }
                }
            }
        }
        return "/img/no-album.png";
    }

    public function extractAlbumArt(string $filepath, array $picture): ?string
    {
        $cover_directory = config("path.storage_covers");
        switch ($picture["image_mime"]) {
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext = ".png";
                break;
        }
        if (isset($ext)) {
            $encoded = base64_encode($picture["data"]);
            $image_string = str_replace(" ", "+", $encoded);
            $image_data = base64_decode($image_string);
            $filename = md5(dirname($filepath)) . $ext;
            if (!file_exists($cover_directory)) {
                if (!mkdir($cover_directory)) {
                    error_log(
                        "Unable to create covers directory. Please check permissions."
                    );
                    exit;
                }
            }
            $storage_path = $cover_directory . "/$filename";
            $public_path = config("path.public_covers") . "/$filename";
            if (!file_exists($storage_path)) {
                file_put_contents($storage_path, $image_data);
            }
            return $public_path;
        }
    }

    public static function search(string $term): ?array
    {
        $results = db()->fetchAll("SELECT tracks.uuid, track_meta.*
			FROM tracks
			INNER JOIN track_meta ON track_meta.track_id = tracks.id
			WHERE title LIKE ? OR
			artist LIKE ? OR
			album LIKE ? OR
			genre LIKE ?
			ORDER BY artist,album,track_number", ...array_fill(0, 4, "%" . htmlspecialchars($term) . "%"));
        return $results ?? [];
    }

    public function transcode()
    {
        $storage_dir = config("path.storage_transcode");
        $md5_file = $storage_dir . '/' . md5($this->name) . '.mp3';
        if (!file_exists($storage_dir)) {
            if (!mkdir($storage_dir)) {
                error_log('failed to create transcode directory!');
            }
        }
        if (!file_exists($md5_file)) {
            $ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout' => 60 * 5,
                'ffmpeg.threads' => 12,
            ]);
            $audio_channels = 2;
            $bitrate = 160;
            $audio = $ffmpeg->open($this->name);
            $format = new FFMpeg\Format\Audio\Mp3('libmp3lame');
            $format
                ->setAudioChannels($audio_channels)
                ->setAudioKiloBitrate($bitrate);
            try {
                $audio->save($format, $md5_file);
                error_log("transcode file: $md5_file");
            } catch (Exception $e) {
                error_log('transcode error: ' . $e->getMessage());
            }
        }
        return $md5_file;
    }
}
