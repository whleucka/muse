<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class SignInController extends Controller
{
	protected function bootstrap(): void
	{
		if (Auth::user()) {
			Auth::redirectHome();
		}
	}

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
		$data = $this->validateRequest([
			"email" => ["required", "email"],
			"password" => ["required"],
		]);
		if ($data) {
			$user = Auth::userAuth($data);
			if ($user) {
				Auth::signIn($user);
			} else {
				$this->request_errors["password"][] = "bad email and/or password";
			}
		}
		return $this->form([
			"email" => $this->request("email")
		]);
	}
}
