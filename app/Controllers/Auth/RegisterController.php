<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class RegisterController extends Controller
{
    /**
     * @param array<int,mixed> $data sign-in form data
     */
    private function form(array $data = []): string
	{
		return $this->render("auth/form/register.php", $data);
	}

	#[Get("/register", "sign-in.index")]
	public function index(): string
	{
		return $this->render("auth/register.php", [
			"form" => $this->form()
		]);
	}

	#[Post("/register", "sign-in.post")]
	public function post(): string
	{
		$data = $this->validateRequest([
			"email" => ["required", "unique|users"],
			"name" => ["required"],
			"password" => ["required"],
			"password_match" => ["required", "match|password"]
		]);
		if ($data) {
			dump($data);
			die("wip");
		}
		return $this->form([
			"email" => $this->request("email"),
			"name" => $this->request("name"),
		]);
	}
}

