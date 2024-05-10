<?php

namespace App\Modules;

use Nebula\Framework\Admin\Module;

class Profile extends Module
{
    public function viewIndex(): string
    {
        throw new \Error('derp');
        return template("profile/index.php", ["name" => user()->name]);
    }
}
