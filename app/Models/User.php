<?php

namespace App\Models;

use Nebula\Framework\Model\Model;

class User extends Model
{
	protected array $columns = [
		"id",
		"name",
		"email",
		"password",
		"updated_at",
		"created_at",
	];

	public function __construct(?string $key = null)
	{
		parent::__construct("users", $key);
	}
}
