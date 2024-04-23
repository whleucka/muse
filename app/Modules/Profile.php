<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Profile extends Module
{
	public function __construct()
	{
		$this->title = "My Profile";
		$this->delete = $this->create = false;
		parent::__construct("profile");
	}

	public function viewIndex(): string
	{
		return template("profile/index.php", ["name" => user()->name]);
	}
}
