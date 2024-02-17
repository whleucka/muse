<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class BasicController extends Controller
{
    #[Get("/", "basic.index")]
    public function index(): string
    {
        $content = template("basic/components/message.php", [
            "message" => "Hello, world! " . time()
        ]);

        return extend("layout/base.php", ["main" => $content]);
    }

    #[Get("/test", "basic.answer", ["api"])]
    public function answer(): string
    {
        return 42;
    }
}
