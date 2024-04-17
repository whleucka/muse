<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class UserType extends Model
{
    protected array $columns = [
		"id",
		"name",
		"permission_level",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("user_types", $key);
    }
}

