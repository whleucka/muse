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
        return template("basic/index.php", [
            "message" => "Hello, world! " . time(),
        ]);
    }

    #[Get("/form", "basic.form")]
    public function form(): string
    {
        return template("basic/form.php");
    }

    #[Post("/form/post", "basic.form")]
    public function post(): string
    {
        dump("Name: " . $this->request('name'));
        dump("Age: " . $this->request('age'));
        return $this->form();
    }
}
