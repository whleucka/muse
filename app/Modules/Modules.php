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
            "Title" => "title",
            "Parent" => "(SELECT m.title
				FROM modules m
				WHERE m.id = modules.parent_module_id) as parent",
            "Updated" => "updated_at",
            "Created" => "created_at",
        ];
        $this->table_format = [
            "updated_at" => "ago",
            "created_at" => "ago",
        ];
        $this->filter_links = [
            "Root" => "parent IS NULL",
            "Children" => "parent IS NOT NULL",
        ];
        $this->table_order_by = "parent_module_id";
        $this->table_sort = "ASC";
        $this->form_columns = [
            "Title" => "title",
            "Path" => "path",
            "Class" => "class_name",
            "SQL Table" => "sql_table",
            "Primary Key" => "primary_key",
            "Item Order" => "item_order",
            "Max Permission Level" => "max_permission_level",
            "Parent Module ID" => "parent_module_id",
        ];
        $this->form_controls = [
            "title" => "input",
            "path" => "input",
            "parent_module_id" => "select",
        ];
        $this->validation_rules = [
            "title" => ["required", "non_empty_string"],
            "path" => ["required", "non_empty_string"],
            "class_name" => ["required", "non_empty_string"],
            "sql_table" => ["required", "non_empty_string"],
            "primary_key" => ["required", "non_empty_string"],
            "item_order" => ["min|0"],
            "max_permission_level" => ["min|0"],
            "parent_module_id" => ["min|0"],
        ];
        $this->select_options = [
            "parent_module_id" => db()->fetchAll("SELECT id as value, title as label
                FROM modules
                WHERE parent_module_id IS NULL AND id != ?", $this->id ?? 'null'),
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
