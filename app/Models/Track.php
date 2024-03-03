<?php

namespace App\Models;

use getID3;
use getid3_lib;

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
}
