<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;
use StellarRouter\Post;

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

    #[Get("/form", "basic.form")]
    public function form(): string
    {
        $content = template("basic/components/form.php");
        return extend("layout/base.php", ["main" => $content]);
    }

    #[Post("/form/post", "basic.form")]
    public function post(): void
    {
        dump("Name: " . $this->request('name'));
        dump("Age: " . $this->request('age'));
    }

    #[Get("/api/answer", "basic.answer", ["api"])]
    public function answer(): string
    {
        return 42;
    }
}
