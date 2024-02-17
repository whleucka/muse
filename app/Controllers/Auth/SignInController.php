<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Group, Get, Post};

#[Group(prefix: "/auth")]
class SignInController extends Controller
{
    /**
     * @param array<int,mixed> $data sign-in form data
     */
    private function form(array $data = []): string
	{
		return template("auth/form/sign-in.php", $data);
	}

	#[Get("/sign-in", "sign-in.index")]
	public function index(): string
	{
		$content = extend("auth/sign-in.php", [
			"form" => $this->form()
		]);

		return extend("layout/base.php", ["main" => $content]);
	}

	#[Post("/sign-in", "sign-in.post")]
	public function post(): string
	{
		return $this->form(["email" => $this->request->get("email")]);
	}
}
