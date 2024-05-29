<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class User extends Model
{
    protected array $columns = [
        "id",
        "uuid",
        "user_type_id",
        "name",
        "email",
        "password",
        "login_at",
        "updated_at",
        "created_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("users", $key);
    }

    public function type(): UserType
    {
        return UserType::find($this->user_type_id);
    }
}
