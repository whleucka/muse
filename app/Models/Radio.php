<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class Radio extends Model
{
    protected array $columns = [
        "id",
        "uuid",
        "station_name",
        "location",
        "src_url",
        "cover_url",
        "updated_at",
        "created_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("radio", $key);
    }
}
