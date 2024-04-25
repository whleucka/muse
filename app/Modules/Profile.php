<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Profile extends Module
{
	public function __construct(object $config)
	{
		$this->create = $this->delete = $this->edit = false;
		parent::__construct($config);
	}

	public function viewIndex(): string
	{
		return template("profile/index.php", ["name" => user()->name]);
	}
}
