<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Profile extends Module
{
    public function viewIndex(): string
    {
        return template("profile/index.php", ["name" => user()->name]);
    }
}
