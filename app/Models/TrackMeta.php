<?php

namespace App\Models;

use getID3;
use getid3_lib;

use Nebula\Framework\Model\Model;

class TrackMeta extends Model
{
    protected array $columns = [
        "id",
        "track_id",
        "cover",
        "filesize",
        "bitrate",
        "mime_type",
        "playtime_string",
        "playtime_seconds",
        "track_number",
        "title",
        "artist",
        "album",
        "genre",
        "year",
        "created_at",
        "updated_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("track_meta", $key);
    }

    public function analyze(): array
    {
        $getID3 = new getID3;
        $tags = $getID3->analyze($this->name);
        getid3_lib::CopyTagsToComments($tags);
        return $tags;
    }
}
