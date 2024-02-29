<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class BasicController extends Controller
{
    #[Get("/", "basic.index")]
    public function index(): string
    {
        return $this->render("basic/index.php", [
            "message" => "Hello, world " . time(),
        ]);
    }

    #[Get("/test", "basic.answer", ["api"])]
    public function answer(): string
    {
        return 42;
    }
}
