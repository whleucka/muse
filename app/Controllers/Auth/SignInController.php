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
		return $this->render("auth/form/sign-in.php", $data);
	}

	#[Get("/sign-in", "sign-in.index")]
	public function index(): string
	{
		return $this->render("auth/sign-in.php", [
			"form" => $this->form()
		]);
	}

	#[Post("/sign-in", "sign-in.post")]
	public function post(): string
	{
		return $this->form(["email" => $this->request("email")]);
	}
}
