<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class BasicController extends Controller
{
    #[Get("/", "basic.index")]
    public function index(): string
    {
        $content = $this->render("basic/index.php", [
            "message" => "Hello, world " . time(),
        ]);

        return $this->render("layout/base.php", ["main" => $content]);
    }

    #[Get("/test", "basic.answer", ["api"])]
    public function answer(): mixed
    {
        return 42;
    }
}
