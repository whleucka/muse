<?php

namespace App\Modules;

use App\Models\Modules as Model;
use Nebula\Framework\Admin\Module;

class Modules extends Module
{
    public function init(): void
    {
        $this->table_columns = [
            "ID" => "id",
            "Enabled" => "enabled",
            "Title" => "title",
            "Parent" => "(SELECT m.title
				FROM modules m
				WHERE m.id = modules.parent_module_id) as parent",
            "Permission Level" => "(SELECT user_types.name
                FROM user_types
                WHERE user_types.permission_level = max_permission_level
                ORDER BY name) as permission_level",
            "Updated" => "updated_at",
            "Created" => "created_at",
        ];
        $this->table_format = [
            "enabled" => "check",
            "updated_at" => "ago",
            "created_at" => "ago",
        ];
        $this->filter_links = [
            "Root" => "parent IS NULL",
            "Children" => "parent IS NOT NULL",
        ];
        $this->table_order_by =
            "parent_module_id,max_permission_level,item_order";
        $this->table_sort = "ASC";
        $this->form_columns = [
            "Enabled" => "enabled",
            "Title" => "title",
            "Path" => "path",
            "Class" => "class_name",
            "SQL Table" => "sql_table",
            "Primary Key" => "primary_key",
            "Item Order" => "item_order",
            "Permission Level" => "max_permission_level",
            "Parent Module ID" => "parent_module_id",
        ];
        $this->form_controls = [
            "enabled" => "checkbox",
            "title" => "input",
            "path" => "input",
            "max_permission_level" => "select",
            "parent_module_id" => "select",
            "item_order" => "number",
        ];
        $this->validation_rules = [
            "title" => ["required"],
            "path" => ["not_empty"],
            "class_name" => ["custom" => [
                "method" => fn($column, $value) => class_exists($value),
                "message" => "Class must exist",
            ]],
            "sql_table" => ["not_empty"],
            "primary_key" => ["not_empty"],
            "item_order" => ["min|0", "required"],
            "max_permission_level" => ["min|0"],
            "parent_module_id" => ["min|0"],
        ];
        $this->select_options = [
            "max_permission_level" => db()->fetchAll("SELECT permission_level as value, name as label
                FROM user_types
                ORDER BY permission_level,name"),
            "parent_module_id" => db()->fetchAll(
                "SELECT id as value, title as label
                FROM modules
                WHERE parent_module_id IS NULL AND id != ?
                ORDER BY title",
                $this->id ?? "NULL"
            ),
        ];
    }

    public function hasDeletePermission(string $id): bool
    {
        if ($id < 8) {
            return false;
        }
        return parent::hasDeletePermission($id);
    }

    public function hasEditPermission(string $id): bool
    {
        $module = Model::find($id);
        if ($module->title === "Modules") {
            return false;
        }

        return parent::hasEditPermission($id);
    }
}
