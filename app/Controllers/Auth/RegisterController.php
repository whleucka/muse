<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Group, Get, Post};

#[Group(prefix: "/auth")]
class RegisterController extends Controller
{
    /**
     * @param array<int,mixed> $data sign-in form data
     */
    private function form(array $data = []): string
	{
		return template("auth/form/register.php", $data);
	}

	#[Get("/register", "sign-in.index")]
	public function index(): string
	{
		$content = extend("auth/register.php", [
			"form" => $this->form()
		]);

		return extend("layout/base.php", ["main" => $content]);
	}

	#[Post("/register", "sign-in.post")]
	public function post(): string
	{
		return $this->form([
			"email" => $this->request->get("email"),
			"name" => $this->request->get("name"),
		]);
	}
}

