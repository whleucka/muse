<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class Session extends Model
{
    protected array $columns = [
        "id",
        "request_uri",
        "ip",
        "user_id",
        "created_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("sessions", $key);
    }
}
