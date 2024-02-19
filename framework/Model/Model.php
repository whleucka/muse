<?php

namespace Nebula\Framework\Model;

use PDO;

class Model
{
	protected string $key_column = "id";
	protected array $columns = [];
	protected array $parameters = [];

	public function __construct(private string $table_name, private ?string $key = null)
	{
		if (!is_null($key)) {
			$this->load();
		}
	}

	public function load(): void
	{
		$result = db()->run($this->selectQuery(), [$this->key])->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			$this->parameters = $result;
		}
	}

	public function getTableName(): string
	{
		return $this->table_name;
	}

	public function selectQuery(): string
	{
		$table_name = $this->getTableName();
		$columns = sprintf("`%s`", implode("`, `", array_values($this->columns)));
		$key_column = $this->key_column;
		$sql = sprintf("SELECT %s FROM %s WHERE %s = ?", $columns, $table_name, $key_column);
		return $sql;
	}

	public function insertQuery(array $data): array
	{
		$table_name = $this->getTableName();
		$columns = sprintf("`%s`", implode("`, `", array_keys($data)));
		$values = implode(", ", array_fill(0, count($data), '?'));
		$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s)", $table_name, $columns, $values);
		return [$sql, array_values($data)];
	}

	public function insert(string $sql, array $values)
	{
		return db()->query($sql, ...$values);
	}

	public static function new(array $data): Model
	{
		$class = get_called_class();
		$model = new $class();

		$result = $model->insert(...$model->insertQuery($data));

		if ($result) {
			$id = db()->lastInsertId();
			return new $class($id);
		}
	}

	public function __isset($name)
	{
		return isset($this->parameters[$name]);
	}

	public function __set($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	public function __get($name)
	{
		return $this->parameters[$name];
	}
}
