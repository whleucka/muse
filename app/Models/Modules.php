<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class Modules extends Model
{
    protected array $columns = [
        "id",
        "title",
        "path",
        "class_name",
        "sql_table",
        "primary_key",
        "item_order",
        "max_permission_level",
        "parent_module_id",
        "updated_at",
        "created_at",
    ];

    public function __construct(?string $key = null)
    {
        parent::__construct("modules", $key);
    }
}
