<?php

namespace Nebula\Framework\Model;

use Exception;

class Model
{
    protected array $columns = [];
    protected string $key_column = "id";

    private array $parameters = [];

    public function __construct(
        private string $table_name,
        private mixed $key = null
    ) {
        if (!is_null($key)) {
            if (empty($this->columns)) {
                throw new Exception("No columns have been set");
            }
            if (!$this->load($key)) {
                throw new Exception("model not found");
            }
        }
    }

    /**
    * Get the model's primary key
    */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
    * Get the model's table name
    */
    public function getTableName(): string
    {
        return $this->table_name;
    }

    /**
    * Get the model's parameters
    */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
    * Load the model
    */
    public function load(mixed $key): bool
    {
        $sql = $this->selectQuery(
            $this->getTableName(),
            $this->columns,
            $this->key_column
        );
        $result = db()->fetch($sql, $key);
        if ($result) {
            $this->parameters = (array) $result;
        }
        return !empty($this->parameters);
    }

    /**
    * Model hydration
    */
    public function hydrate()
    {
        $this->load($this->getKey());
    }

    /**
    * Model hydration (alias)
    */
    public function refresh(): void
    {
        $this->hydrate();
    }

    /**
    * Get all models from db
    */
    public static function all()
    {
        $class = get_called_class();
        $model = new $class();
        $sql = $model->selectAllQuery(
            $model->getTableName(),
            $model->columns,
        );
        $results = db()->fetchAll($sql);
        $key_column = $model->key_column;
        return array_map(fn($result) => $model->find($result->$key_column), $results);
    }

    /**
    * Find a model from the db by primary key
    */
    public static function find(mixed $key)
    {
        $class = get_called_class();
        try {
            $model = new $class($key);
            return $model;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
    * Find a model from the db by attribute
    */
    public static function findByAttribute(string $attribute, mixed $key)
    {
        $class = get_called_class();
        $model = new $class();
        $sql = $model->selectQuery(
            $model->getTableName(),
            $model->columns,
            $attribute
        );
        $result = db()->fetch($sql, $key);
        if ($result) {
            $key_column = $model->key_column;
            return new $model($result->$key_column);
        }
    }

    /**
    * Create a new model
    */
    public static function new(array $data): Model
    {
        $class = get_called_class();
        $model = new $class();

        $sql = $model->insertQuery($model->getTableName(), $data);
        $result = db()->query($sql, ...array_values($data));

        if ($result) {
            $id = db()->lastInsertId();
            return new $class($id);
        }
    }

    /**
    * Save model to db
    */
    public function save(): bool
    {
        $sql = $this->updateQuery(
            $this->getTableName(),
            $this->getParameters(),
            $this->key_column
        );
        $data = [...$this->getParameters(), $this->getKey()];
        $result = db()->query($sql, ...array_values($data));
        if ($result) {
            $this->hydrate();
            return true;
        }
        return false;
    }

    /**
    * Delete model from db
    */
    public function delete(): bool
    {
        $sql = $this->deleteQuery($this->getTableName(), $this->key_column);
        $result = db()->query($sql, $this->getKey());
        return $result ? true : false;
    }

    /**
    * Get the select query
    */
    private function selectQuery(
        string $table_name,
        array $data,
        string $key_column
    ): string {
        $columns = implode(", ", array_values($data));
        $sql = sprintf(
            "SELECT %s FROM `%s` WHERE %s = ?",
            $columns,
            $table_name,
            $key_column
        );
        return $sql;
    }

    /**
    * Get the update query
    */
    private function updateQuery(
        string $table_name,
        array $data,
        string $key_column
    ): string {
        $updates = array_map(fn($column) => "$column = ?", array_keys($data));
        $columns = implode(", ", $updates);
        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s = ?",
            $table_name,
            $columns,
            $key_column
        );
        return $sql;
    }

    /**
    * Get the insert query
    */
    private function insertQuery(string $table_name, array $data): string
    {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $table_name,
            $columns,
            $values
        );
        return $sql;
    }

    /**
    * Get the delete query
    */
    private function deleteQuery(string $table_name, string $key_column): string
    {
        $sql = sprintf(
            "DELETE FROM `%s` WHERE %s = ?",
            $table_name,
            $key_column
        );
        return $sql;
    }

    public function __isset(mixed $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function __set(mixed $name, mixed $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function __get(mixed $name): mixed
    {
        return $this->parameters[$name];
    }
}
