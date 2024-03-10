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

	public static function search(string $term): ?array
	{
		return db()->fetchAll("SELECT tracks.uuid, track_meta.*, '/img/no-album.png' as cover
			FROM tracks
			INNER JOIN track_meta ON track_meta.track_id = tracks.id
			WHERE title LIKE ? OR
			artist LIKE ? OR
			album LIKE ? OR
			genre LIKE ?
			ORDER BY artist,album,track_number", ...array_fill(0, 4, "%".htmlspecialchars($term)."%"));
	}

}
